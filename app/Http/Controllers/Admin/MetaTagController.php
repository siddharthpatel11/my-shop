<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaTag;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MetaTagController extends Controller
{
    /**
     * Define the static routes/pages that are manageable.
     */
    protected $staticPages = [
        'frontend.home' => 'Home Page',
        'contact' => 'Contact Us Page',
        'frontend.products.index' => 'Our Products Page',
    ];

    public function index()
    {
        // 1. Get Static Pages
        $pages = collect();
        foreach ($this->staticPages as $route => $name) {
            $pages->push((object)[
                'identifier' => $route,
                'name' => $name,
                'type' => 'Static Page',
                'meta' => MetaTag::where('page_identifier', $route)->first()
            ]);
        }

        // 2. Get Dynamic CMS Pages
        $dynamicPages = Page::all();
        foreach ($dynamicPages as $page) {
            $identifier = 'page:' . $page->slug;
            $pages->push((object)[
                'identifier' => $identifier,
                'name' => $page->title,
                'type' => 'CMS Page',
                'meta' => MetaTag::where('page_identifier', $identifier)->first()
            ]);
        }

        return view('admin.meta-tags.index', compact('pages'));
    }

    public function edit($identifier)
    {
        // Determine name and type based on identifier
        $name = 'Unknown Page';
        $type = 'Unknown';

        if (array_key_exists($identifier, $this->staticPages)) {
            $name = $this->staticPages[$identifier];
            $type = 'Static Page';
        } elseif (str_starts_with($identifier, 'page:')) {
            $slug = substr($identifier, 5);
            $page = Page::where('slug', $slug)->firstOrFail();
            $name = $page->title;
            $type = 'CMS Page';
        }

        $metaTag = MetaTag::firstOrCreate(
            ['page_identifier' => $identifier]
        );

        return view('admin.meta-tags.edit', compact('metaTag', 'name', 'type', 'identifier'));
    }

    public function update(Request $request, $identifier)
    {
        $request->validate([
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_key' => 'nullable|string|max:255',
            'seo_canonical' => 'nullable|url|max:255',
            'seo_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'og_key' => 'nullable|string|max:255',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $metaTag = MetaTag::where('page_identifier', $identifier)->firstOrFail();

        $data = $request->except(['seo_image', 'og_image', 'remove_seo_image', 'remove_og_image']);

        // Handle SEO Image Update/Remove
        if ($request->has('remove_seo_image') && $request->remove_seo_image == '1') {
            if ($metaTag->seo_image && File::exists(public_path('images/seo/' . $metaTag->seo_image))) {
                File::delete(public_path('images/seo/' . $metaTag->seo_image));
            }
            $data['seo_image'] = null;
        } elseif ($request->hasFile('seo_image')) {
            if ($metaTag->seo_image && File::exists(public_path('images/seo/' . $metaTag->seo_image))) {
                File::delete(public_path('images/seo/' . $metaTag->seo_image));
            }
            $seoImage = $request->file('seo_image');
            $seoImageName = time() . '_seo_' . uniqid() . '.' . $seoImage->getClientOriginalExtension();
            $seoImage->move(public_path('images/seo'), $seoImageName);
            $data['seo_image'] = $seoImageName;
        }

        // Handle OG Image Update/Remove
        if ($request->has('remove_og_image') && $request->remove_og_image == '1') {
            if ($metaTag->og_image && File::exists(public_path('images/seo/' . $metaTag->og_image))) {
                File::delete(public_path('images/seo/' . $metaTag->og_image));
            }
            $data['og_image'] = null;
        } elseif ($request->hasFile('og_image')) {
            if ($metaTag->og_image && File::exists(public_path('images/seo/' . $metaTag->og_image))) {
                File::delete(public_path('images/seo/' . $metaTag->og_image));
            }
            $ogImage = $request->file('og_image');
            $ogImageName = time() . '_og_' . uniqid() . '.' . $ogImage->getClientOriginalExtension();
            $ogImage->move(public_path('images/seo'), $ogImageName);
            $data['og_image'] = $ogImageName;
        }

        $metaTag->update($data);

        return redirect()->route('admin.meta-tags.index')->with('success', 'Meta tags updated successfully.');
    }
}






// Batha static page ne meta mukva and show krva(new ke old statis page show)
// protected function getStaticPages()
//     {
//         $pages = [];
//         $routes = \Illuminate\Support\Facades\Route::getRoutes()->get('GET');

//         foreach ($routes as $route) {
//             $uri = $route->uri();
//             $name = $route->getName();
//             $middleware = $route->middleware();

//             // Exclude dynamic routes (with parameters)
//             if (str_contains($uri, '{')) continue;
//             // Exclude routes without a name
//             if (!$name) continue;

//             // Exclude API, ignition, sanctum
//             if (str_starts_with($uri, 'api') || str_starts_with($uri, '_ignition') || str_starts_with($uri, 'sanctum')) continue;

//             // Admin routes use 'auth' middleware
//             if (in_array('auth', $middleware)) continue;

//             // Exclude action-oriented routes (form submissions, AJAX, verifications)
//             $ignoredKeywords = ['export', 'verify', 'setup', 'logout', 'add', 'remove', 'update', 'clear', 'discount', 'checkout', 'submit'];
//             $skip = false;
//             foreach ($ignoredKeywords as $kw) {
//                 if (str_contains($uri, $kw)) {
//                     $skip = true;
//                     break;
//                 }
//             }
//             if ($skip) continue;

//             // Generate a readable name from URI
//             $readableName = $uri === '/' ? 'Home Page' : ucwords(str_replace(['-', '/'], ' ', trim($uri, '/'))) . ' Page';

//             $pages[$name] = $readableName;
//         }

//         return $pages;
//     }

//     public function index()
//     {
//         // 1. Get Static Pages
//         $staticPages = $this->getStaticPages();
//         $pages = collect();
//         foreach ($staticPages as $route => $name) {
//             $pages->push((object)[
//                 'identifier' => $route,
//                 'name' => $name,
//                 'type' => 'Static Page',
//                 'meta' => MetaTag::where('page_identifier', $route)->first()
//             ]);
//         }

//         // 2. Get Dynamic CMS Pages
//         $dynamicPages = Page::all();
//         foreach ($dynamicPages as $page) {
//             $identifier = 'page:' . $page->slug;
//             $pages->push((object)[
//                 'identifier' => $identifier,
//                 'name' => $page->title,
//                 'type' => 'CMS Page',
//                 'meta' => MetaTag::where('page_identifier', $identifier)->first()
//             ]);
//         }

//         return view('admin.meta-tags.index', compact('pages'));
//     }

//     public function edit($identifier)
//     {
//         // Determine name and type based on identifier
//         $name = 'Unknown Page';
//         $type = 'Unknown';

//         $staticPages = $this->getStaticPages();
//         if (array_key_exists($identifier, $staticPages)) {
//             $name = $staticPages[$identifier];
//             $type = 'Static Page';
//         } elseif (str_starts_with($identifier, 'page:')) {
//             $slug = substr($identifier, 5);
//             $page = Page::where('slug', $slug)->firstOrFail();
//             $name = $page->title;
//             $type = 'CMS Page';
//         }

//         $metaTag = MetaTag::firstOrCreate(
//             ['page_identifier' => $identifier]
//         );

//         return view('admin.meta-tags.edit', compact('metaTag', 'name', 'type', 'identifier'));
//     }

//     public function update(Request $request, $identifier)
//     {
//         $request->validate([
//             'seo_title' => 'nullable|string|max:255',
//             'seo_description' => 'nullable|string',
//             'seo_key' => 'nullable|string|max:255',
//             'seo_canonical' => 'nullable|url|max:255',
//             'seo_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

//             'og_title' => 'nullable|string|max:255',
//             'og_description' => 'nullable|string',
//             'og_key' => 'nullable|string|max:255',
//             'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
//         ]);

//         $metaTag = MetaTag::where('page_identifier', $identifier)->firstOrFail();

//         $data = $request->except(['seo_image', 'og_image', 'remove_seo_image', 'remove_og_image']);

//         // Handle SEO Image Update/Remove
//         if ($request->has('remove_seo_image') && $request->remove_seo_image == '1') {
//             if ($metaTag->seo_image && File::exists(public_path('images/seo/' . $metaTag->seo_image))) {
//                 File::delete(public_path('images/seo/' . $metaTag->seo_image));
//             }
//             $data['seo_image'] = null;
//         } elseif ($request->hasFile('seo_image')) {
//             if ($metaTag->seo_image && File::exists(public_path('images/seo/' . $metaTag->seo_image))) {
//                 File::delete(public_path('images/seo/' . $metaTag->seo_image));
//             }
//             $seoImage = $request->file('seo_image');
//             $seoImageName = time() . '_seo_' . uniqid() . '.' . $seoImage->getClientOriginalExtension();
//             $seoImage->move(public_path('images/seo'), $seoImageName);
//             $data['seo_image'] = $seoImageName;
//         }

//         // Handle OG Image Update/Remove
//         if ($request->has('remove_og_image') && $request->remove_og_image == '1') {
//             if ($metaTag->og_image && File::exists(public_path('images/seo/' . $metaTag->og_image))) {
//                 File::delete(public_path('images/seo/' . $metaTag->og_image));
//             }
//             $data['og_image'] = null;
//         } elseif ($request->hasFile('og_image')) {
//             if ($metaTag->og_image && File::exists(public_path('images/seo/' . $metaTag->og_image))) {
//                 File::delete(public_path('images/seo/' . $metaTag->og_image));
//             }
//             $ogImage = $request->file('og_image');
//             $ogImageName = time() . '_og_' . uniqid() . '.' . $ogImage->getClientOriginalExtension();
//             $ogImage->move(public_path('images/seo'), $ogImageName);
//             $data['og_image'] = $ogImageName;
//         }

//         $metaTag->update($data);

//         return redirect()->route('admin.meta-tags.index')->with('success', 'Meta tags updated successfully.');
//     }
