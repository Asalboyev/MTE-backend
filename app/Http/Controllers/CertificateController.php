<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\Lang;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public $title = 'Наши каталоги';
    public $route_name = 'catalogs';
    public $route_parameter = 'catalog';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search'); // Foydalanuvchi kiritgan qidiruv so‘rovi
        $certificates = Certificate::query();

        if (!empty($search)) {
            $certificates->where('title', 'like', "%$search%"); // Qidiruvni `title` bo‘yicha amalga oshirish
        }

        $certificates = $certificates->latest()->paginate(12); // Natijalarni paginatsiya qilish
        $languages = Lang::all();

        return view('app.certificates.index', [
            'title' => $this->title,
            'route_name' => $this->route_name,
            'route_parameter' => $this->route_parameter,
            'certificates' => $certificates,
            'languages' => $languages,
            'search' => $search // Qidiruv maydoniga oldingi qiymatni yuborish
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $langs = Lang::all();

        return view('app.certificates.create', [
            'title' => $this->title,
            'route_name' => $this->route_name,
            'route_parameter' => $this->route_parameter,
            'langs' => $langs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title.' . $this->main_lang->code => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->with([
                'success' => false,
                'message' => 'Ошибка валидации'
            ]);
        }

        $data['slug'] = Str::slug($data['title'][$this->main_lang->code], '-');

        // Agar slug allaqachon mavjud bo'lsa, vaqt tamg'asi qo'shiladi
        if (Certificate::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $data['slug'] . '-' . time();
        }

        // Rasm ma'lumotlarini sozlash
        $data['img'] = $data['dropzone_images'] ?? null;

        // Faylni `public/certificates` ichida saqlash
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Faqat bitta fayl yuklashni tekshiramiz
            if (!is_array($file)) {
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $filePath = public_path('certificates'); // `public/certificates` katalogi

                // Agar katalog mavjud bo‘lmasa, uni yaratamiz
                if (!file_exists($filePath)) {
                    mkdir($filePath, 0777, true);
                }

                // Faylni yuklash
                $file->move($filePath, $fileName);

                // Fayl manzilini saqlash
                $data['file'] = 'certificates/' . $fileName;
            } else {
                return response()->json(['error' => 'Faqat bitta fayl yuklashingiz mumkin!'], 400);
            }
        } else {
            return response()->json(['error' => 'Fayl topilmadi!'], 400);
        }

        Certificate::create($data);

        return redirect()->route('catalogs.index')->with([
            'success' => true,
            'message' => 'Успешно сохранен'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $langs = Lang::all();
        $certificate = Certificate::findOrFail($id); // Agar ID topilmasa, 404 error qaytaradi
        return view('app.certificates.edit', [
            'title' => $this->title,
            'route_name' => $this->route_name,
            'route_parameter' => $this->route_parameter,
            'langs' => $langs,
            'certificate' => $certificate,
            'old_file' => $certificate->file, // Faylni o‘tqazyapmiz
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Request dan faqat kerakli maydonlarni olish
        $data = $request->only(['title', 'dropzone_images']);

        // Validatsiya qilish
        $validator = Validator::make($data, [
            'title.' . $this->main_lang->code => 'required',
            'file' => 'nullable|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->with([
                'success' => false,
                'message' => 'Ошибка валидации'
            ]);
        }

        // Sertifikatni topish
        $certificate = Certificate::find($id);

        if (!$certificate) {
            return redirect()->route('catalogs.index')->with([
                'success' => false,
                'message' => 'Сертификат не найден'
            ]);
        }

        // Rasmni yangilash (dropzone orqali)
        $data['img'] = isset($data['dropzone_images']) && is_array($data['dropzone_images'])
            ? implode(',', $data['dropzone_images'])
            : $certificate->img;

        // Faylni yuklash va eski faylni o'chirish
        if ($request->hasFile('file')) {
            if ($certificate->file && file_exists(public_path($certificate->file))) {
                unlink(public_path($certificate->file));
            }

            $file = $request->file('file');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = public_path('certificates');

            if (!file_exists($filePath)) {
                mkdir($filePath, 0777, true);
            }

            $file->move($filePath, $fileName);
            $data['file'] = 'certificates/' . $fileName;
        } else {
            $data['file'] = is_string($certificate->file) ? $certificate->file : null;
        }

        // Ma'lumotlar massivida faqat string qiymatlar borligiga ishonch hosil qilish
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = json_encode($value);
            }
        }

        // Sertifikatni yangilash
        $certificate->update($data);

        return redirect()->route('catalogs.index')->with([
            'success' => true,
            'message' => 'Успешно обновлен'
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Sertifikatni id orqali topish
        $certificate = Certificate::find($id);

        // Agar sertifikat mavjud bo'lmasa, xatolik xabarini ko'rsatish
        if (!$certificate) {
            return back()->with([
                'success' => false,
                'message' => 'Сертификат не найден'
            ]);
        }

        // Sertifikatni o'chirish
        $certificate->delete();

        // Qayta yo'naltirish va muvaffaqiyat xabarini ko'rsatish
        return back()->with([
            'success' => true,
            'message' => 'Успешно удален'
        ]);
    }

}
