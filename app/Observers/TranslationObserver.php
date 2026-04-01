<?php

namespace App\Observers;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Log;

class TranslationObserver
{
    /**
     * Handle the model "saving" event.
     * This intercepts creation AND updates before they reach the database!
     */
    public function saving($model)
    {
        try {
            $tr = new GoogleTranslate('en');

            // Handle standard 'name' field
            if (!empty($model->name) && $model->isDirty('name')) {
                // Determine if admin has manually entered a translation override
                if (empty($model->name_gu) || !$model->isDirty('name_gu')) {
                    $model->name_gu = $tr->setTarget('gu')->translate($model->name);
                }

                if (empty($model->name_hi) || !$model->isDirty('name_hi')) {
                    $model->name_hi = $tr->setTarget('hi')->translate($model->name);
                }

                if (empty($model->name_sa) || !$model->isDirty('name_sa')) {
                    $model->name_sa = $tr->setTarget('sa')->translate($model->name);
                }

                if (empty($model->name_bn) || !$model->isDirty('name_bn')) {
                    $model->name_bn = $tr->setTarget('bn')->translate($model->name);
                }
            }

            // Handle specific 'detail' field for Products
            if (!empty($model->detail) && $model->isDirty('detail')) {
                if (empty($model->detail_gu) || !$model->isDirty('detail_gu')) {
                    $model->detail_gu = $tr->setTarget('gu')->translate($model->detail);
                }

                if (empty($model->detail_hi) || !$model->isDirty('detail_hi')) {
                    $model->detail_hi = $tr->setTarget('hi')->translate($model->detail);
                }

                if (empty($model->detail_sa) || !$model->isDirty('detail_sa')) {
                    $model->detail_sa = $tr->setTarget('sa')->translate($model->detail);
                }

                if (empty($model->detail_bn) || !$model->isDirty('detail_bn')) {
                    $model->detail_bn = $tr->setTarget('bn')->translate($model->detail);
                }
            }
        } catch (\Exception $e) {
            // Failsafe: if translation API hits rate limits, log error and continue saving normally
            Log::error("Auto-Translation failed: " . $e->getMessage());
        }
    }

    // darect loop add sort cat
    // public function saving($model)
    // {
    //     try {
    //         $tr = new GoogleTranslate('en');

    //         if (!empty($model->name) && $model->isDirty('name')) {
    //             foreach (['gu', 'hi', 'sa', 'bn'] as $lang) {
    //                 $col = "name_{$lang}";
    //                 if (empty($model->{$col}) || !$model->isDirty($col)) {
    //                     $model->{$col} = $tr->setTarget($lang)->translate($model->name);
    //                 }
    //             }
    //         }

    //         if (!empty($model->detail) && $model->isDirty('detail')) {
    //             foreach (['gu', 'hi', 'sa', 'bn'] as $lang) {
    //                 $col = "detail_{$lang}";
    //                 if (property_exists($model, $col) || isset($model->getAttributes()[$col])) {
    //                     if (empty($model->{$col}) || !$model->isDirty($col)) {
    //                         $model->{$col} = $tr->setTarget($lang)->translate($model->detail);
    //                     }
    //                 }
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         \Illuminate\Support\Facades\Log::error("Auto-Translation failed: " . $e->getMessage());
    //     }
    // }
}
