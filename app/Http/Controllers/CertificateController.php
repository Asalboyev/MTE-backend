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
    public function index()
    {
        $certificates = Certificate::latest()->paginate(12);
        $languages = Lang::all();

        return view('app.certificates.index', [
            'title' => $this->title,
            'route_name' => $this->route_name,
            'route_parameter' => $this->route_parameter,
            'certificates' => $certificates,
            'languages' => $languages
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
            'title.' . $this->main_lang->code => 'required'
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
        $certificate = Certificate::find($id);
        return view('app.certificates.edit', [
            'title' => $this->title,
            'route_name' => $this->route_name,
            'route_parameter' => $this->route_parameter,
            'langs' => $langs,
            'certificate' => $certificate,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Request dan barcha ma'lumotlarni olish
        $data = $request->all();

        // Validatsiya qilish
        $validator = Validator::make($data, [
            'title.' . $this->main_lang->code => 'required'
        ]);

        // Agar validatsiya xatosi bo'lsa, foydalanuvchiga qaytish
        if ($validator->fails()) {
            return back()->withInput()->with([
                'success' => false,
                'message' => 'Ошибка валидации'
            ]);
        }

        // Rasm mavjud bo'lsa, yangilanadi. Bo'lmasa null bo'ladi
        $data['img'] = $data['dropzone_images'] ?? null;

        // Sertifikatni topish va yangilash
        $certificate = Certificate::find($id);

        // Agar sertifikat mavjud bo'lmasa, qayta yo'naltirish
        if (!$certificate) {
            return redirect()->route('catalogs.index')->with([
                'success' => false,
                'message' => 'Сертификат не найден'
            ]);
        }

        // Sertifikatni yangilash
        $certificate->update($data);

        // Qayta yo'naltirish va muvaffaqiyat xabarini ko'rsatish
        return redirect()->route('catalogs.index')->with([
            'success' => true,
            'message' => 'Успешно сохранен'
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
