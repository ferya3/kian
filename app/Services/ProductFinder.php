<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * موتور «محصول مناسب پروژه خود را پیدا کنید».
 *
 * به‌جای فیلتر سخت‌گیرانه‌ای که ممکن است هیچ نتیجه‌ای برنگرداند، هر محصول
 * امتیاز تطابق می‌گیرد و دلیل انتخابش هم به کاربر نشان داده می‌شود. این کار
 * باعث می‌شود کاربر هیچ‌وقت به بن‌بست «محصولی یافت نشد» نخورد.
 */
class ProductFinder
{
    /** @var array<string, int> */
    protected array $weights;

    public function __construct()
    {
        $this->weights = config('kian.finder.weights');
    }

    /**
     * @param  array{project_type?:string|null, wall_type?:string|null, thickness?:int|string|null, insulation?:string|null}  $criteria
     * @return Collection<int, array{product: Product, score: int, reasons: array<int, string>, gaps: array<int, string>}>
     */
    public function search(array $criteria, int $limit = 3): Collection
    {
        $products = Product::query()->active()->with('category')->get();

        return $products
            ->map(fn (Product $product) => $this->evaluate($product, $criteria))
            ->sortByDesc(fn (array $row) => [$row['score'], $row['product']->is_featured])
            ->values()
            ->take($limit);
    }

    /**
     * @param  array<string, mixed>  $criteria
     * @return array{product: Product, score: int, reasons: array<int, string>, gaps: array<int, string>}
     */
    public function evaluate(Product $product, array $criteria): array
    {
        $score = 0;
        $reasons = [];
        $gaps = [];

        // ۱) نوع پروژه
        if ($type = $criteria['project_type'] ?? null) {
            $label = config("kian.finder.project_types.$type.label", $type);
            if (in_array($type, $product->project_types ?? [], true)) {
                $score += $this->weights['project_type'];
                $reasons[] = "مناسب پروژه‌های {$label}";
            } else {
                $gaps[] = "برای {$label} گزینه‌ی بهینه‌تری هم داریم";
            }
        }

        // ۲) نوع دیوار
        if ($wall = $criteria['wall_type'] ?? null) {
            $label = config("kian.finder.wall_types.$wall.label", $wall);
            if (in_array($wall, $product->wall_types ?? [], true)) {
                $score += $this->weights['wall_type'];
                $reasons[] = "طراحی‌شده برای {$label}";
            } else {
                $gaps[] = "کاربرد اصلی این محصول {$label} نیست";
            }
        }

        // ۳) ضخامت — تطابق دقیق، یا نزدیک‌ترین ضخامت با امتیاز نسبی
        if ($thickness = $criteria['thickness'] ?? null) {
            $requested = (float) $thickness;
            $delta = abs($product->thicknessCm() - $requested);
            $max = $this->weights['thickness'];

            if ($delta < 0.01) {
                $score += $max;
                $reasons[] = 'ضخامت دقیقاً برابر با نیاز شما ('.$this->num($requested).' سانتی‌متر)';
            } elseif ($delta <= 5) {
                $score += (int) round($max * (1 - $delta / 5) * 0.7);
                $gaps[] = 'ضخامت '.$this->num($product->thicknessCm()).' سانتی‌متر به‌جای '.$this->num($requested);
            }
        }

        // ۴) سطح عایق حرارتی — یک پله بالاتر هم قابل قبول است
        if ($insulation = $criteria['insulation'] ?? null) {
            $ladder = array_keys(config('kian.finder.insulation_levels'));
            $want = array_search($insulation, $ladder, true);
            $has = array_search($product->insulation_level, $ladder, true);
            $max = $this->weights['insulation'];

            if ($want !== false && $has !== false) {
                $step = $has - $want;
                if ($step === 0) {
                    $score += $max;
                    $reasons[] = 'سطح عایق حرارتی دقیقاً مطابق درخواست';
                } elseif ($step > 0) {
                    $score += (int) round($max * 0.85);
                    $reasons[] = 'عایق حرارتی بالاتر از حد درخواستی شما';
                } else {
                    $score += (int) round($max * max(0, 1 + $step / 2) * 0.4);
                    $gaps[] = 'برای عایق‌کاری بیشتر، بلوک ضخیم‌تر را ببینید';
                }
            }
        }

        // اگر کاربر هیچ فیلتری انتخاب نکرده باشد، شاخص‌های کلی محصول ملاک است.
        if ($score === 0 && ! array_filter($criteria)) {
            $score = (int) round(($product->thermal_score + $product->strength_score) / 2);
        }

        return [
            'product' => $product,
            'score' => min(100, $score),
            'reasons' => $reasons,
            'gaps' => $gaps,
        ];
    }

    /** برچسب خوانا برای معیارهای انتخاب‌شده — در نتایج نمایش داده می‌شود. */
    public function criteriaLabels(array $criteria): array
    {
        $labels = [];

        if ($v = $criteria['project_type'] ?? null) {
            $labels[] = config("kian.finder.project_types.$v.label", $v);
        }
        if ($v = $criteria['wall_type'] ?? null) {
            $labels[] = config("kian.finder.wall_types.$v.label", $v);
        }
        if ($v = $criteria['thickness'] ?? null) {
            $labels[] = $this->num((float) $v).' سانتی‌متر';
        }
        if ($v = $criteria['insulation'] ?? null) {
            $labels[] = 'عایق '.config("kian.finder.insulation_levels.$v.label", $v);
        }

        return $labels;
    }

    protected function num(float $value): string
    {
        return rtrim(rtrim(number_format($value, 1), '0'), '.');
    }
}
