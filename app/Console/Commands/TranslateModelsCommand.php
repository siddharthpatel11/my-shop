<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use App\Models\Product;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateModelsCommand extends Command
{
    protected $signature = 'translate:models';
    protected $description = 'Automatically translates existing database records using Google Translate';

    public function handle()
    {
        $this->info("Starting background translations...");
        $tr = new GoogleTranslate('en');

        // Products
        $products = Product::whereNull('name_gu')->orWhereNull('name_hi')->orWhereNull('name_sa')->orWhereNull('name_bn')->get();
        foreach ($products as $model) {
            $this->line("Translating Product: " . $model->name);
            if (!empty($model->name)) {
                if (empty($model->name_gu)) $model->name_gu = $tr->setTarget('gu')->translate($model->name);
                if (empty($model->name_hi)) $model->name_hi = $tr->setTarget('hi')->translate($model->name);
                if (empty($model->name_sa)) $model->name_sa = $tr->setTarget('sa')->translate($model->name);
                if (empty($model->name_bn)) $model->name_bn = $tr->setTarget('bn')->translate($model->name);
            }
            if (!empty($model->detail)) {
                if (empty($model->detail_gu)) $model->detail_gu = $tr->setTarget('gu')->translate($model->detail);
                if (empty($model->detail_hi)) $model->detail_hi = $tr->setTarget('hi')->translate($model->detail);
                if (empty($model->detail_sa)) $model->detail_sa = $tr->setTarget('sa')->translate($model->detail);
                if (empty($model->detail_bn)) $model->detail_bn = $tr->setTarget('bn')->translate($model->detail);
            }
            $model->saveQuietly();
        }

        //darect short cat add
        // $products = Product::get();
        // foreach ($products as $model) {
        //     $dirty = false;
        //     foreach (['gu', 'hi', 'sa', 'bn'] as $lang) {
        //         if (!empty($model->name) && empty($model->{"name_{$lang}"})) {
        //             $model->{"name_{$lang}"} = $tr->setTarget($lang)->translate($model->name);
        //             $dirty = true;
        //         }
        //         if (!empty($model->detail) && empty($model->{"detail_{$lang}"})) {
        //             $model->{"detail_{$lang}"} = $tr->setTarget($lang)->translate($model->detail);
        //             $dirty = true;
        //         }
        //     }
        //     if ($dirty) {
        //         $this->line("Translating Product: " . $model->name);
        //         $model->saveQuietly();
        //     }
        // }

        // Categories
        $categories = Category::whereNull('name_gu')->orWhereNull('name_hi')->orWhereNull('name_sa')->orWhereNull('name_bn')->get();
        foreach ($categories as $model) {
            $this->line("Translating Category: " . $model->name);
            if (!empty($model->name)) {
                if (empty($model->name_gu)) $model->name_gu = $tr->setTarget('gu')->translate($model->name);
                if (empty($model->name_hi)) $model->name_hi = $tr->setTarget('hi')->translate($model->name);
                if (empty($model->name_sa)) $model->name_sa = $tr->setTarget('sa')->translate($model->name);
                if (empty($model->name_bn)) $model->name_bn = $tr->setTarget('bn')->translate($model->name);
            }
            $model->saveQuietly();
        }

        // Colors
        $colors = Color::whereNull('name_gu')->orWhereNull('name_hi')->orWhereNull('name_sa')->orWhereNull('name_bn')->get();
        foreach ($colors as $model) {
            $this->line("Translating Color: " . $model->name);
            if (!empty($model->name)) {
                if (empty($model->name_gu)) $model->name_gu = $tr->setTarget('gu')->translate($model->name);
                if (empty($model->name_hi)) $model->name_hi = $tr->setTarget('hi')->translate($model->name);
                if (empty($model->name_sa)) $model->name_sa = $tr->setTarget('sa')->translate($model->name);
                if (empty($model->name_bn)) $model->name_bn = $tr->setTarget('bn')->translate($model->name);
            }
            $model->saveQuietly();
        }

        // Sizes
        $sizes = Size::whereNull('name_gu')->orWhereNull('name_hi')->orWhereNull('name_sa')->orWhereNull('name_bn')->get();
        foreach ($sizes as $model) {
            $this->line("Translating Size: " . $model->name);
            if (!empty($model->name)) {
                if (empty($model->name_gu)) $model->name_gu = $tr->setTarget('gu')->translate($model->name);
                if (empty($model->name_hi)) $model->name_hi = $tr->setTarget('hi')->translate($model->name);
                if (empty($model->name_sa)) $model->name_sa = $tr->setTarget('sa')->translate($model->name);
                if (empty($model->name_bn)) $model->name_bn = $tr->setTarget('bn')->translate($model->name);
            }
            $model->saveQuietly();
        }

        $this->info("✅ All existing records translated successfully!");
    }
}
