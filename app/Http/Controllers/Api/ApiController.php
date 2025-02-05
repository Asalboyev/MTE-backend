<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Certificate;
use App\Models\Member;
use App\Models\ProductsCategory;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\SiteInfo;
use App\Models\Product;
use App\Models\Application;
use Illuminate\Support\Facades\App;

class ApiController extends Controller
{
    public function get_banner()
    {
        // Foydalanuvchi tilini olish
        $locale = App::getLocale();

        // Postlarni oxirgi qo'shilganidan boshlab olish va 10 tadan paginate qilish
        $banners = Brand::latest()->paginate(10);

        // Agar postlar topilmasa, 404 xatolikni qaytaradi
        if ($banners->isEmpty()) {
            return response()->json([
                'message' => 'No records found'
            ], 404);
        }

        // Postlarni foydalanuvchi tiliga moslashtirish
        $translatedPosts = collect($banners->items())->map(function ($banner) use ($locale) {
            return [
                'id' => $banner->id,
                'title' => $banner->title[$locale] ?? null,
                'desc' => $banner->desc[$locale] ?? null,
                'images' => [
                    'lg' => $banner->lg_img, // Katta rasm uchun URL
                    'md' => $banner->md_img, // O‘rta rasm uchun URL
                    'sm' => $banner->sm_img, // Kichik rasm uchun URL
                ],
            ];
        });

        // Postlar va paginate ma'lumotlarini JSON formatida qaytarish
        return response()->json([
            'data' => $translatedPosts,             // Tilga mos postlar
            'total' => $banners->total(),             // Umumiy postlar soni
            'per_page' => $banners->perPage(),        // Har bir sahifadagi postlar soni
            'current_page' => $banners->currentPage(), // Hozirgi sahifa raqami
            'last_page' => $banners->lastPage(),      // Oxirgi sahifa raqami
            'next_page_url' => $banners->nextPageUrl(), // Keyingi sahifa URLi
            'prev_page_url' => $banners->previousPageUrl(), // Oldingi sahifa URLi
        ]);
    }
    public function translations()
    {
        // Foydalanuvchi tilini olish
        $locale = App::getLocale();

        // Tarjimalarni olish, har bir tarjimani uning guruhidan olish
        $banners = Translation::with('translationGroup')->latest()->get();

        // Agar tarjimalar topilmasa, 404 xatolikni qaytaradi
        if ($banners->isEmpty()) {
            return response()->json([
                'message' => 'No records found'
            ], 404);
        }

        // Tarjimalarni formatlash: key: value shaklida, group.sub_text.key formatida
        $translations = $banners->mapWithKeys(function ($banner) use ($locale) {
            return [
                $banner->translationGroup->sub_text . '.' . $banner->key => $banner->val[$locale] ?? null
            ];
        });

        // JSON formatida qaytarish
        return response()->json($translations);
    }

    public function get_team()
    {
        // Foydalanuvchi tilini olish
        $locale = App::getLocale();

        // Postlarni oxirgi qo'shilganidan boshlab olish va 10 tadan paginate qilish
        $member = Member::latest()->paginate(10);

        // Agar postlar topilmasa, 404 xatolikni qaytaradi
        if ($member->isEmpty()) {
            return response()->json([
                'message' => 'No records found'
            ], 404);
        }

        // Postlarni foydalanuvchi tiliga moslashtirish
        $translatedPosts = collect($member->items())->map(function ($banner) use ($locale) {
            return [
                'id' => $banner->id,
                'name' => $banner->name[$locale] ?? null,
                'position' => $banner->position[$locale] ?? null,
                'work_time' => $banner->work_time[$locale] ?? null,
                'images' => [
                    'lg' => $banner->lg_img, // Katta rasm uchun URL
                    'md' => $banner->md_img, // O‘rta rasm uchun URL
                    'sm' => $banner->sm_img, // Kichik rasm uchun URL
                ],
                'phone_number' => $banner->phone_number ?? null,
                'instagram_link' => $banner->instagram_link ?? null,
                'telegram_link' => $banner->telegram_link ?? null,
                'linkedin_link' => $banner->linkedin_link ?? null,
                'facebook_link' => $banner->facebook_link ?? null,

            ];
        });

        // Postlar va paginate ma'lumotlarini JSON formatida qaytarish
        return response()->json([
            'data' => $translatedPosts,             // Tilga mos postlar
            'total' => $member->total(),             // Umumiy postlar soni
            'per_page' => $member->perPage(),        // Har bir sahifadagi postlar soni
            'current_page' => $member->currentPage(), // Hozirgi sahifa raqami
            'last_page' => $member->lastPage(),      // Oxirgi sahifa raqami
            'next_page_url' => $member->nextPageUrl(), // Keyingi sahifa URLi
            'prev_page_url' => $member->previousPageUrl(), // Oldingi sahifa URLi
        ]);
    }
    public function show_team($id)
    {
        // Foydalanuvchi tilini olish
        $locale = App::getLocale();

        // Berilgan ID bo‘yicha a'zoni olish
        $member = Member::find($id);

        // Agar a'zo topilmasa, 404 xatolikni qaytaradi
        if (!$member) {
            return response()->json([
                'message' => 'Member not found'
            ], 404);
        }

        // A'zo ma'lumotlarini foydalanuvchi tiliga moslashtirish
        $translatedMember = [
            'id' => $member->id,
            'name' => $member->name[$locale] ?? null,
            'position' => $member->position[$locale] ?? null,
            'work_time' => $member->work_time[$locale] ?? null,
            'images' => [
                'lg' => $member->lg_img, // Katta rasm uchun URL
                'md' => $member->md_img, // O‘rta rasm uchun URL
                'sm' => $member->sm_img, // Kichik rasm uchun URL
            ],
            'phone_number' => $member->phone_number ?? null,
            'instagram_link' => $member->instagram_link ?? null,
            'telegram_link' => $member->telegram_link ?? null,
            'linkedin_link' => $member->linkedin_link ?? null,
            'facebook_link' => $member->facebook_link ?? null,
        ];

        // A'zo ma'lumotlarini JSON formatida qaytarish
        return response()->json($translatedMember);
    }



    public function get_posts()
    {
        // Foydalanuvchi tilini olish
        $locale = App::getLocale();

        // Postlarni oxirgi qo'shilganidan boshlab olish va 10 tadan paginate qilish
        $posts = Post::latest()->with('postImages')->paginate(10);

        // Agar postlar topilmasa, 404 xatolikni qaytaradi
        if ($posts->isEmpty()) {
            return response()->json([
                'message' => 'No records found'
            ], 404);
        }

        // Postlarni foydalanuvchi tiliga moslashtirish
        $translatedPosts = collect($posts->items())->map(function ($post) use ($locale) {
            return [
                'id' => $post->id,
                'title' => $post->title[$locale] ?? null,
                'desc' => $post->desc[$locale] ?? null,

             'images' => $post->postImages->map(function ($image) {
             return [
                'lg' => $image->lg_img, // Katta o'lchamdagi rasm URL
                'md' => $image->md_img, // O'rta o'lchamdagi rasm URL
                'sm' => $image->sm_img, // Kichik o'lchamdagi rasm URL
            ];
                  })->toArray(),
                'date' => $post->date,
                'views_count' => $post->views_count,
                'slug' => $post->slug,
                'meta_keywords' => $post->meta_keywords,
            ];
        });

        // Postlar va paginate ma'lumotlarini JSON formatida qaytarish
        return response()->json([
            'data' => $translatedPosts,             // Tilga mos postlar
            'total' => $posts->total(),             // Umumiy postlar soni
            'per_page' => $posts->perPage(),        // Har bir sahifadagi postlar soni
            'current_page' => $posts->currentPage(), // Hozirgi sahifa raqami
            'last_page' => $posts->lastPage(),      // Oxirgi sahifa raqami
            'next_page_url' => $posts->nextPageUrl(), // Keyingi sahifa URLi
            'prev_page_url' => $posts->previousPageUrl(), // Oldingi sahifa URLi
        ]);
    }

    public function show_post($slug)
    {
        // Foydalanuvchi tilini olish
        $locale = App::getLocale();

        // Slug orqali postni olish
        $post = Post::where('slug', $slug)->first();

        if (is_null($post)) {
            return response()->json(['message' => 'Post not found or URL is not null'], 404);
        }

        // Oldingi postni olish (ID qiymati hozirgi postdan kichik bo'lgan eng oxirgi post)
        $previousPost = Post::where('id', '<', $post->id)->orderBy('id', 'desc')->first();

        // Keyingi postni olish (ID qiymati hozirgi postdan katta bo'lgan eng birinchi post)
        $nextPost = Post::where('id', '>', $post->id)->orderBy('id', 'asc')->first();

        // Postni foydalanuvchi tiliga moslashtirish
        $translatedPost = [
            'id' => $post->id,
            'title' => $post->title[$locale] ?? null,
            'desc' => $post->desc[$locale] ?? null,
            'images' => $post->postImages->map(function ($image) {
                return [
                    'lg' => $image->lg_img, // Katta o'lchamdagi rasm URL
                    'md' => $image->md_img, // O'rta o'lchamdagi rasm URL
                    'sm' => $image->sm_img, // Kichik o'lchamdagi rasm URL
                ];
            })->toArray(),
            'slug' => $post->slug,
            'date' => $post->date,
            'views_count' => $post->views_count,
            'meta_keywords' => $post->meta_keywords,
            // Oldingi post
            'previous' => $previousPost ? [
                'name' => $previousPost->title[$locale] ?? null,
                'slug' => $previousPost->slug,
            ] : null,
            // Keyingi post
            'next' => $nextPost ? [
                'name' => $nextPost->title[$locale] ?? null,
                'slug' => $nextPost->slug,
            ] : null,
        ];

        return response()->json($translatedPost);
    }



    public function get_catalogs()
    {
        // Foydalanuvchi tilini olish
        $locale = App::getLocale();

        // Postlarni oxirgi qo'shilganidan boshlab olish va 10 tadan paginate qilish
        $certificate = Certificate::latest()->paginate(10);

        // Agar postlar topilmasa, 404 xatolikni qaytaradi
        if ($certificate->isEmpty()) {
            return response()->json([
                'message' => 'No records found'
            ], 404);
        }

        // Postlarni foydalanuvchi tiliga moslashtirish
        $translatedPosts = collect($certificate->items())->map(function ($certificate) use ($locale) {
            return [
                'id' => $certificate->id,
                'title' => $certificate->title[$locale] ?? null, // Mahsulotning nomi (locale bo'yicha)
                'desc' => $certificate->desc[$locale] ?? null, // Mahsulotning ta'rifi (locale bo'yicha)

                // Rasmning to'liq URL manzili, turli o'lchamlar uchun
                'photo' => [
                    'lg' => $certificate->img ? url('/upload/images/' . $certificate->img) : null, // Katta o'lchamdagi rasm
                    'md' => $certificate->img ? url('/upload/images/600/' . $certificate->img) : null, // O'rtacha o'lchamdagi rasm
                    'sm' => $certificate->img ? url('/upload/images/200/' . $certificate->img) : null, // Kichik o'lchamdagi rasm
                ],
                'date' => $certificate->date,
                'views_count' => $certificate->views_count,
                'slug' => $certificate->slug,
            ];
        });


        // Postlar va paginate ma'lumotlarini JSON formatida qaytarish
        return response()->json([
            'data' => $translatedPosts,             // Tilga mos postlar
            'total' => $certificate->total(),             // Umumiy postlar soni
            'per_page' => $certificate->perPage(),        // Har bir sahifadagi postlar soni
            'current_page' => $certificate->currentPage(), // Hozirgi sahifa raqami
            'last_page' => $certificate->lastPage(),      // Oxirgi sahifa raqami
            'next_page_url' => $certificate->nextPageUrl(), // Keyingi sahifa URLi
            'prev_page_url' => $certificate->previousPageUrl(), // Oldingi sahifa URLi
        ]);
    }

    public function show_catalogs($slug){ // Foydalanuvchi tilini olish
        $locale = App::getLocale();

        // Slug orqali postni olish
        $certificate = Certificate::where('slug', $slug)->first();

        if (is_null($certificate)) {
            return response()->json(['message' => 'Post not found or URL is not null'], 404);
        }

        // Postni foydalanuvchi tiliga moslashtirish
        $translatedPost = [
            'id' => $certificate->id,
            'title' => $certificate->title[$locale] ?? null,
            'desc' => $certificate->desc[$locale] ?? null,
            'photo' => [
                'lg' => $certificate->img ? url('/upload/images/' . $certificate->img) : null, // Katta o'lchamdagi rasm
                'md' => $certificate->img ? url('/upload/images/600/' . $certificate->img) : null, // O'rtacha o'lchamdagi rasm
                'sm' => $certificate->img ? url('/upload/images/200/' . $certificate->img) : null, // Kichik o'lchamdagi rasm
            ],
            'slug' => $certificate->slug,
        ];

        return response()->json($translatedPost);
    }
    // start categories
    public function get_categories()
    {
        $locale = App::getLocale();

        // Asosiy kategoriyalarni olish (faqat parent_id = null bo'lganlar)
        $categories = ProductsCategory::whereNull('parent_id')->with('children')->latest()->paginate(10);

        if ($categories->isEmpty()) {
            return response()->json([
                'message' => 'No records found'
            ], 404);
        }

        // Kategoriyalarni map qilish (asosiy va farzand kategoriyalarni tarjimalar bilan)
        $translatedPosts = collect($categories->items())->map(function ($category) use ($locale) {
            return [
                'id' => $category->id,
                'title' => $category->title[$locale] ?? null,
                'desc' => $category->desc[$locale] ?? null,
                'children' => $category->children->map(function ($child) use ($locale) {
                    return [
                        'id' => $child->id,
                        'title' => $child->title[$locale] ?? null,
                        'desc' => $child->desc[$locale] ?? null,
                        'images' => [
                            'lg' => $child->lg_img,
                            'md' => $child->md_img,
                            'sm' => $child->sm_img,
                        ],
                    ];
                }),
                'images' => [
                    'lg' => $category->lg_img, // Katta rasm uchun URL
                    'md' => $category->md_img, // O‘rta rasm uchun URL
                    'sm' => $category->sm_img, // Kichik rasm uchun URL
                ],
                'in_main' => $category->in_main,
                'view' => $category->view,
                'slug' => $category->slug,
            ];
        });

        return response()->json([
            'data' => $translatedPosts,              // Tilga mos kategoriyalar
            'total' => $categories->total(),        // Umumiy kategoriyalar soni
            'per_page' => $categories->perPage(),   // Har bir sahifadagi kategoriyalar soni
            'current_page' => $categories->currentPage(), // Hozirgi sahifa raqami
            'last_page' => $categories->lastPage(), // Oxirgi sahifa raqami
            'next_page_url' => $categories->nextPageUrl(), // Keyingi sahifa URLi
            'prev_page_url' => $categories->previousPageUrl(), // Oldingi sahifa URLi
        ]);
    }

    public function show_categories($slug)
    {
        // Foydalanuvchi tilini olish
        $locale = App::getLocale();

        // Kategoriyani ID orqali topish
        $category = ProductsCategory::with('children')->where('slug', $slug)->first();

        // Agar kategoriya topilmasa, xato xabarini qaytarish
        if (is_null($category)) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Kategoriya ma'lumotlarini foydalanuvchi tiliga moslashtirish
        $translatedCategory = [
            'id' => $category->id,
            'title' => $category->title[$locale] ?? null, // Foydalanuvchi tiliga mos sarlavha
            'desc' => $category->desc[$locale] ?? null,   // Foydalanuvchi tiliga mos tavsif
            'children' => $category->children->map(function ($child) use ($locale) {
                return [
                    'id' => $child->id,
                    'title' => $child->title[$locale] ?? null,
                    'desc' => $child->desc[$locale] ?? null,
                    'images' => [
                        'lg' => $child->lg_img, // Katta rasm URL
                        'md' => $child->md_img, // O'rta rasm URL
                        'sm' => $child->sm_img, // Kichik rasm URL
                    ],
                ];
            }),
            'images' => [
                'lg' => $category->lg_img, // Katta rasm URL
                'md' => $category->md_img, // O'rta rasm URL
                'sm' => $category->sm_img, // Kichik rasm URL
            ],
            'in_main' => $category->in_main,
            'view' => $category->view,
            'slug' => $category->slug,

        ];

        // Ma'lumotlarni JSON formatida qaytarish
        return response()->json($translatedCategory);
    }
    public function show_categor_product($id)
    {
        // Foydalanuvchi tilini olish
        $locale = App::getLocale();

        // Kategoriyani ID orqali topish
        $category = ProductsCategory::with(['children', 'products.productImages'])->find($id);

        // Agar kategoriya topilmasa, xato xabarini qaytarish
        if (is_null($category)) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Mahsulotlarni 12 ta qilib paginate qilish
        $paginatedProducts = $category->products()->with('productImages')->paginate(12);

        // Kategoriya ma'lumotlarini foydalanuvchi tiliga moslashtirish
        $translatedCategory = [
            'id' => $category->id,
            'title' => $category->title[$locale] ?? null,
            'desc' => $category->desc[$locale] ?? null,
            'info' => $category->info[$locale] ?? $category->info, //

            'children' => $category->children->map(function ($child) use ($locale) {
                return [
                    'id' => $child->id,
                    'title' => $child->title[$locale] ?? null,
                    'desc' => $child->desc[$locale] ?? null,
                    'images' => [
                        'lg' => $child->lg_img,
                        'md' => $child->md_img,
                        'sm' => $child->sm_img,
                    ],
                ];
            }),
            'images' => [
                'lg' => $category->lg_img,
                'md' => $category->md_img,
                'sm' => $category->sm_img,
            ],
            'in_main' => $category->in_main,
            'view' => $category->view,
            'products' => [
                'data' => $paginatedProducts->map(function ($product) use ($locale) {
                    return [
                        'id' => $product->id,
                        'title' => $product->title[$locale] ?? $product->title,
                        'description' => $product->desc[$locale] ?? $product->desc,
                        'info' => $product->info[$locale] ?? null,
                        'slug' => $product->slug,
                        'images' => $product->productImages->map(function ($image) {
                            return [
                                'lg' => $image->lg_img,
                                'md' => $image->md_img,
                                'sm' => $image->sm_img,
                            ];
                        }),
                        'meta_keywords' => $product->meta_keywords[$locale] ?? $product->meta_keywords,
                        'meta_desc' => $product->meta_desc[$locale] ?? $product->meta_desc,
                        'stock' => $product->stock,
                    ];
                }),
                'pagination' => [
                    'current_page' => $paginatedProducts->currentPage(),
                    'last_page' => $paginatedProducts->lastPage(),
                    'per_page' => $paginatedProducts->perPage(),
                    'total' => $paginatedProducts->total(),
                ]
            ]
        ];

        // Ma'lumotlarni JSON formatida qaytarish
        return response()->json($translatedCategory);
    }

    //end categories




    public function get_products()
    {
        $locale = App::getLocale();

        // Barcha mahsulotlarni ularning kategoriyalari va rasm aloqalari bilan olish
        $products = Product::with(['productsCategories', 'productImages'])->latest()->paginate(10);

        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'No products found'
            ], 404);
        }

        // Mahsulotlarni foydalanuvchi tiliga moslashtirish
        $translatedProducts = collect($products->items())->map(function ($product) use ($locale) {
            return [
                'id' => $product->id,
                'title' => $product->title[$locale] ?? $product->title, // Foydalanuvchi tiliga mos nom
                'description' => $product->desc[$locale] ?? $product->desc, //
                'info' => $product->info[$locale] ?? $product->info, //
                // Tavsif
                // Tavsif
                'stock' => $product->stock, // Ombordagi qoldiq
                'images' => $product->productImages->map(function ($image) {
                    return [
                        'lg' => $image->lg_img, // Katta rasm uchun URL
                        'md' => $image->md_img, // O'rta rasm uchun URL
                        'sm' => $image->sm_img, // Kichik rasm uchun URL
                    ];
                }),
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'title' => $product->category->title[$locale] ?? $product->category->title, // Kategoriya nomi
                    'desc' => $product->category->desc[$locale] ?? $product->category->desc,   // Kategoriya tavsifi
                    'images' => [
                        'lg' => $product->category->lg_img, // Katta rasm uchun URL
                        'md' => $product->category->md_img, // O'rta rasm uchun URL
                        'sm' => $product->category->sm_img, // Kichik rasm uchun URL
                    ],
                ] : null,
                'slug' => $product->slug, // Ombordagi qoldiq
                'meta_keywords' => $product->meta_keywords[$locale] ?? $product->meta_keywords, // Tavsif
                'meta_desc' => $product->meta_desc[$locale] ?? $product->meta_desc,
            ];
        });

        // JSON formatida natijalarni qaytarish
        return response()->json([
            'data' => $translatedProducts,           // Foydalanuvchi tiliga mos mahsulotlar
            'total' => $products->total(),          // Umumiy mahsulotlar soni
            'per_page' => $products->perPage(),     // Har bir sahifadagi mahsulotlar soni
            'current_page' => $products->currentPage(), // Hozirgi sahifa raqami
            'last_page' => $products->lastPage(),   // Oxirgi sahifa raqami
            'next_page_url' => $products->nextPageUrl(), // Keyingi sahifa URL
            'prev_page_url' => $products->previousPageUrl(), // Oldingi sahifa URL
        ]);
    }


    public function show_products($slug)
    {
        $locale = App::getLocale();

        $product = Product::with(['productsCategories', 'productImages'])->where('slug', $slug)->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'title' => $product->title[$locale] ?? $product->title,
            'description' => $product->desc[$locale] ?? $product->desc,
            'stock' => $product->stock,
            'images' => $product->productImages->map(function ($image) {
                return [
                    'lg' => $image->lg_img,
                    'md' => $image->md_img,
                    'sm' => $image->sm_img,
                ];
            }),
            'categories' => $product->productsCategories->map(function ($category) use ($locale) {
                return [
                    'id' => $category->id,
                    'title' => $category->title[$locale] ?? $category->title,
                    'desc' => $category->desc[$locale] ?? $category->desc,
                ];
            }),
            'slug' => $product->slug,
            'meta_keywords' => $product->meta_keywords[$locale] ?? $product->meta_keywords,
            'meta_desc' => $product->meta_desc[$locale] ?? $product->meta_desc,
        ]);
    }

    public function getCompany()
    {
        // Hozirgi foydalanuvchi tilini olish
        $locale = App::getLocale();

        // SiteInfo ma'lumotlarini olish (oxirgi kiritilgan)
        $site_info = SiteInfo::latest()->first();

        if (!$site_info) {
            return response()->json(['message' => 'Site information not found'], 404);
        }

        // Foydalanuvchi tiliga moslashtirilgan ma'lumotlar
        $translatedSiteInfo = [
            'id' => $site_info->id,
            'title' => $site_info->title[$locale] ?? $site_info->title,  // Foydalanuvchi tiliga mos nom
            'logo' => $site_info->logo,  // Logo
            'logo_dark' => $site_info->logo_dark,  // Qorong'u logo
            'desc' => $site_info->desc[$locale] ?? $site_info->desc,  // Tavsif
            'address' => $site_info->address[$locale] ?? $site_info->address,  // Manzil
            'phone_number' => $site_info->phone_number,  // Telefon raqami
            'email' => $site_info->email,  // Elektron pochta
            'work_time' => $site_info->work_time[$locale] ?? $site_info->work_time ?? null, // Ish vaqti
            'map' => $site_info->map,  // Xarita
            'exchange' => $site_info->exchange,  // Kurs o'zgarishlari
            'favicon' => $site_info->favicon,  // Favicon
            'telegram' => $site_info->telegram,  // Telegram
            'instagram' => $site_info->instagram,  // Instagram
            'facebook' => $site_info->facebook,  // Facebook
            'youtube' => $site_info->youtube,  // YouTube
        ];

        // JSON formatida natijalarni qaytarish
        return response()->json([
            'data' => $translatedSiteInfo,  // Foydalanuvchi tiliga mos kompaniya ma'lumotlari
        ]);
    }
    public function store(Request $request)
{
    // Requestdagi ma'lumotlarni tekshirish (validatsiya)
    $request->validate([
        'name' => 'required|string|max:255',
        'phone_number' => 'nullable|string|max:20',
        'message' => 'required|string',
    ]);

    // Ma'lumotni saqlash
    $contact = Application::create([
        'name' => $request->name,
        'phone_number' => $request->phone_number,
        'message' => $request->message,
    ]);

    // Yangi Contact ma'lumotlarini qaytarish
    return response()->json([
        'message' => 'Contact information saved successfully.',
        'data' => $contact,
    ], 201);
}


//-
}
