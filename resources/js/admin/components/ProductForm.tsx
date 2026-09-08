import React, { useState, useCallback, useEffect } from 'react';
import { useForm, useFieldArray } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { useDropzone } from 'react-dropzone';
import { toast } from 'sonner';

const variantSchema = z.object({
  size: z.string().optional(),
  color: z.string().optional(),
  stock: z.number().min(0).default(0),
});

const productSchema = z.object({
  name: z.string().min(1, "Nama produk wajib diisi"),
  description: z.string().optional(),
  price: z.number().min(0, "Harga minimal 0"),
  stock: z.number().min(0, "Stok minimal 0"),
  category: z.string().optional(),
  new_category: z.string().optional(),
  gender: z.enum(['Pria', 'Wanita', 'Unisex']).optional(),
  variants: z.array(variantSchema).optional(),
}).refine(data => data.category || data.new_category, {
  message: "Pilih kategori atau buat kategori baru",
  path: ["new_category"]
}).superRefine((data, ctx) => {
  if (data.variants && data.variants.length > 0) {
    const totalVariantStock = data.variants.reduce((sum, v) => sum + (v.stock || 0), 0);
    if (totalVariantStock !== data.stock) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        message: `Total stok varian (${totalVariantStock}) harus sama dengan Stok Utama (${data.stock})`,
        path: ["stock"]
      });
    }
  }
});

type ProductFormValues = z.infer<typeof productSchema>;

export default function ProductForm() {
  const [categories, setCategories] = useState<{id: number, name: string}[]>([]);
  const [isNewCategory, setIsNewCategory] = useState(false);
  const [preview, setPreview] = useState<string | null>(null);

  const { register, control, handleSubmit, formState: { errors, isSubmitting }, setValue, watch } = useForm<ProductFormValues>({
    resolver: zodResolver(productSchema),
    defaultValues: { stock: 0, variants: [] }
  });

  const { fields: variantFields, append: appendVariant, remove: removeVariant } = useFieldArray({
    control,
    name: "variants"
  });

  const variantsWatch = watch('variants');
  const hasVariants = variantsWatch && variantsWatch.length > 0;
  
  useEffect(() => {
    if (hasVariants) {
      const total = variantsWatch.reduce((sum, v) => sum + (Number(v.stock) || 0), 0);
      setValue('stock', total, { shouldValidate: true });
    }
  }, [variantsWatch, hasVariants, setValue]);

  useEffect(() => {
    fetch('/admin/api/categories')
      .then(res => res.json())
      .then(data => setCategories(data))
      .catch(err => console.error("Failed to load categories"));
  }, []);

  const onDrop = useCallback((acceptedFiles: File[]) => {
    if (acceptedFiles.length > 0) {
      const file = acceptedFiles[0];
      const previewUrl = URL.createObjectURL(file);
      setPreview(previewUrl);
      toast.success('Gambar berhasil ditambahkan.');
    }
  }, []);

  const { getRootProps, getInputProps, isDragActive } = useDropzone({ onDrop, accept: { 'image/*': [] }, multiple: false });

  const onSubmit = async (data: ProductFormValues) => {
    try {
      const res = await fetch('/admin/api/products', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
        },
        body: JSON.stringify(data),
      });

      if (!res.ok) {
        const errData = await res.json().catch(() => ({}));
        throw new Error(errData.message || 'Gagal menyimpan');
      }
      
      toast.success('Produk berhasil disimpan!');
      window.location.href = '/admin/products';
    } catch (error: any) {
      console.error(error);
      toast.error(error.message || 'Terjadi kesalahan saat menyimpan produk.');
    }
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6 bg-white p-6 rounded-lg shadow-sm">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label className="block text-sm font-medium text-gray-700">Nama Produk</label>
          <input {...register('name')} className="mt-1 block w-full border rounded p-2" />
          {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name.message}</p>}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700">Harga (Rp)</label>
          <input type="number" {...register('price', { valueAsNumber: true })} className="mt-1 block w-full border rounded p-2" />
          {errors.price && <p className="text-red-500 text-xs mt-1">{errors.price.message}</p>}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700">Stok Utama (Total)</label>
          <input 
            type="number" 
            {...register('stock', { valueAsNumber: true })} 
            readOnly={hasVariants}
            className={`mt-1 block w-full border rounded p-2 ${hasVariants ? 'bg-gray-100 cursor-not-allowed' : 'bg-gray-50'}`} 
          />
          <p className="text-[10px] text-gray-500 mt-1">*Jika Anda menambahkan varian (ukuran/warna), stok ini akan otomatis terupdate dari jumlah stok varian.</p>
          {errors.stock && <p className="text-red-500 text-xs mt-1">{errors.stock.message}</p>}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
          <div className="flex gap-2">
            {!isNewCategory ? (
              <select {...register('category')} className="block w-full border rounded p-2">
                <option value="">Pilih Kategori...</option>
                {categories.map(c => <option key={c.id} value={c.name}>{c.name}</option>)}
              </select>
            ) : (
              <input {...register('new_category')} className="block w-full border rounded p-2" placeholder="Nama kategori baru" />
            )}
            <button 
              type="button" 
              onClick={() => {
                setIsNewCategory(!isNewCategory);
                setValue('category', undefined);
                setValue('new_category', undefined);
              }}
              className="bg-gray-200 px-3 py-2 rounded text-sm whitespace-nowrap"
            >
              {isNewCategory ? 'Pilih Kategori' : '+ Baru'}
            </button>
          </div>
          {errors.new_category && <p className="text-red-500 text-xs mt-1">{errors.new_category.message}</p>}
        </div>
      </div>

      {/* Varian Produk */}
      <div className="border border-gray-200 p-4 rounded-lg bg-gray-50/50">
        <div className="flex justify-between items-center mb-4">
          <h3 className="font-semibold text-gray-800 text-sm uppercase tracking-widest">Varian Produk (Ukuran & Warna)</h3>
          <button 
            type="button" 
            onClick={() => appendVariant({ size: '', color: '', stock: 0 })}
            className="text-xs bg-black text-white px-3 py-1 rounded"
          >
            + Tambah Varian
          </button>
        </div>
        
        {variantFields.length === 0 ? (
          <p className="text-xs text-gray-500">Belum ada varian. Produk ini tidak memiliki pilihan ukuran atau warna khusus.</p>
        ) : (
          <div className="space-y-3">
            {variantFields.map((field, index) => (
              <div key={field.id} className="flex flex-wrap md:flex-nowrap gap-3 items-end border-b border-gray-200 pb-3">
                <div className="flex-1">
                  <label className="block text-xs font-medium text-gray-500 mb-1">Ukuran (Opsional)</label>
                  <input {...register(`variants.${index}.size`)} placeholder="S, M, L, 42..." className="block w-full border rounded p-2 text-sm" />
                </div>
                <div className="flex-1">
                  <label className="block text-xs font-medium text-gray-500 mb-1">Warna (Opsional)</label>
                  <input {...register(`variants.${index}.color`)} placeholder="Hitam, Cokelat..." className="block w-full border rounded p-2 text-sm" />
                </div>
                <div className="w-24">
                  <label className="block text-xs font-medium text-gray-500 mb-1">Stok</label>
                  <input type="number" {...register(`variants.${index}.stock`, { valueAsNumber: true })} className="block w-full border rounded p-2 text-sm" />
                </div>
                <div>
                  <button type="button" onClick={() => removeVariant(index)} className="bg-red-100 text-red-600 px-3 py-2 rounded text-sm hover:bg-red-200">
                    Hapus
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700 mb-2">Upload Gambar (Drag & Drop)</label>
        <div {...getRootProps()} className={`border-2 border-dashed p-10 text-center rounded-lg cursor-pointer ${isDragActive ? 'border-blue-500 bg-blue-50' : 'border-gray-300'}`}>
          <input {...getInputProps()} />
          {preview ? (
             <div className="flex flex-col items-center justify-center">
               <img src={preview} alt="Preview" className="h-40 object-contain rounded mb-4 shadow-sm" />
               <p className="text-sm text-gray-500">Klik atau tarik gambar lain untuk mengganti</p>
             </div>
          ) : (
             <p className="text-gray-500">Tarik gambar ke sini, atau klik untuk memilih file</p>
          )}
        </div>
      </div>

      <button 
        type="submit" 
        disabled={isSubmitting}
        className="w-full bg-black text-white p-3 rounded-lg hover:bg-gray-800 disabled:opacity-50 font-bold uppercase tracking-widest text-sm"
      >
        {isSubmitting ? 'Menyimpan...' : 'Simpan Produk'}
      </button>
    </form>
  );
}
