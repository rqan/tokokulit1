import React, { useState, useCallback, useEffect } from 'react';
import { useForm, useFieldArray } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
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
  const [previews, setPreviews] = useState<(string | null)[]>([null]);

  const { register, control, handleSubmit, formState: { errors, isSubmitting }, setValue, watch } = useForm<ProductFormValues>({
    resolver: zodResolver(productSchema),
    defaultValues: { stock: 0, variants: [] }
  });

  const { fields: variantFields, append: appendVariant, remove: removeVariant } = useFieldArray({
    control,
    name: "variants"
  });

  const variantsWatch = watch('variants') || [];
  const hasVariants = variantsWatch.length > 0;
  const serializedStocks = JSON.stringify(variantsWatch.map(v => v?.stock || 0));
  
  useEffect(() => {
    if (hasVariants) {
      const total = variantsWatch.reduce((sum, v) => sum + (Number(v?.stock) || 0), 0);
      setValue('stock', total, { shouldValidate: true });
    }
  }, [serializedStocks, hasVariants, setValue]);

  useEffect(() => {
    fetch('/admin/api/categories')
      .then(res => res.json())
      .then(data => setCategories(data))
      .catch(err => console.error("Failed to load categories"));
  }, []);

  const handleImageUpload = (e: React.ChangeEvent<HTMLInputElement>, idx: number) => {
    if (e.target.files && e.target.files.length > 0) {
      const file = e.target.files[0];
      const newPreviews = [...previews];
      newPreviews[idx] = URL.createObjectURL(file);
      
      // If it's the last slot and we haven't reached 5 slots, add a new empty slot
      if (idx === previews.length - 1 && previews.length < 5) {
        newPreviews.push(null);
      }
      setPreviews(newPreviews);
      toast.success('Gambar berhasil ditambahkan.');
    }
  };

  const removeImage = (idx: number) => {
    const newPreviews = [...previews];
    newPreviews.splice(idx, 1);
    
    // Ensure there's always at least one empty slot if less than 5
    if (newPreviews.length < 5 && newPreviews[newPreviews.length - 1] !== null) {
      newPreviews.push(null);
    }
    // If we deleted the only item, keep one empty slot
    if (newPreviews.length === 0) {
      newPreviews.push(null);
    }
    setPreviews(newPreviews);
  };

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
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6 bg-lightBg dark:bg-darkBg p-6 rounded-xl border border-lightBorder dark:border-darkBorder shadow-sm text-lightMain dark:text-darkMain">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label className="block text-xs font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted mb-2">Nama Produk</label>
          <input {...register('name')} className="mt-1 block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors" />
          {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name.message}</p>}
        </div>

        <div>
          <label className="block text-xs font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted mb-2">Harga (Rp)</label>
          <input type="number" {...register('price', { valueAsNumber: true })} className="mt-1 block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors" />
          {errors.price && <p className="text-red-500 text-xs mt-1">{errors.price.message}</p>}
        </div>

        <div>
          <label className="block text-xs font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted mb-2">Stok Utama (Total)</label>
          <input 
            type="number" 
            {...register('stock', { valueAsNumber: true })} 
            readOnly={hasVariants}
            className={`mt-1 block w-full border border-lightBorder dark:border-darkBorder rounded p-2 outline-none transition-colors ${hasVariants ? 'bg-black/5 dark:bg-white/5 cursor-not-allowed text-lightMuted dark:text-darkMuted' : 'bg-transparent focus:border-lightMain dark:focus:border-darkMain'}`} 
          />
          <p className="text-[10px] text-lightMuted dark:text-darkMuted mt-1">*Jika Anda menambahkan varian (ukuran/warna), stok ini otomatis terisi.</p>
          {errors.stock && <p className="text-red-500 text-xs mt-1">{errors.stock.message}</p>}
        </div>

        <div>
          <label className="block text-xs font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted mb-2">Kategori</label>
          <div className="flex gap-2">
            {!isNewCategory ? (
              <select {...register('category')} className="block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
                <option value="" className="bg-lightBg dark:bg-darkBg">Pilih Kategori...</option>
                {categories.map(c => <option key={c.id} value={c.name} className="bg-lightBg dark:bg-darkBg">{c.name}</option>)}
              </select>
            ) : (
              <input {...register('new_category')} className="block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors" placeholder="Nama kategori baru" />
            )}
            <button 
              type="button" 
              onClick={() => {
                setIsNewCategory(!isNewCategory);
                setValue('category', undefined);
                setValue('new_category', undefined);
              }}
              className="border border-lightBorder dark:border-darkBorder bg-black/5 dark:bg-white/5 hover:bg-black/10 dark:hover:bg-white/10 px-3 py-2 rounded text-xs font-bold uppercase tracking-widest whitespace-nowrap transition-colors"
            >
              {isNewCategory ? 'Pilih Kategori' : '+ Baru'}
            </button>
          </div>
          {errors.new_category && <p className="text-red-500 text-xs mt-1">{errors.new_category.message}</p>}
        </div>

        <div>
          <label className="block text-xs font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted mb-2">Gender</label>
          <select {...register('gender')} className="block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors">
            <option value="Unisex" className="bg-lightBg dark:bg-darkBg">Unisex</option>
            <option value="Pria" className="bg-lightBg dark:bg-darkBg">Pria</option>
            <option value="Wanita" className="bg-lightBg dark:bg-darkBg">Wanita</option>
          </select>
        </div>
      </div>

      {/* Varian Produk */}
      <div className="border border-lightBorder dark:border-darkBorder p-6 rounded-lg bg-black/5 dark:bg-white/5">
        <div className="flex justify-between items-center mb-4">
          <h3 className="font-bold text-xs uppercase tracking-[0.2em] text-lightMain dark:text-darkMain">Varian Produk (Ukuran & Warna)</h3>
          <button 
            type="button" 
            onClick={() => appendVariant({ size: '', color: '', stock: 0 })}
            className="text-[10px] bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg px-3 py-1.5 rounded font-bold tracking-widest uppercase hover:opacity-90"
          >
            + Tambah Varian
          </button>
        </div>
        
        {variantFields.length === 0 ? (
          <p className="text-xs text-lightMuted dark:text-darkMuted">Belum ada varian. Produk ini tidak memiliki pilihan ukuran atau warna khusus.</p>
        ) : (
          <div className="space-y-4">
            {variantFields.map((field, index) => (
              <div key={field.id} className="flex flex-wrap md:flex-nowrap gap-4 items-end border-b border-lightBorder dark:border-darkBorder pb-4">
                <div className="flex-1">
                  <label className="block text-[10px] font-bold tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-2">Ukuran (Opsional)</label>
                  <input {...register(`variants.${index}.size`)} placeholder="S, M, L, 42..." className="block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 text-sm outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors" />
                </div>
                <div className="flex-1">
                  <label className="block text-[10px] font-bold tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-2">Warna (Opsional)</label>
                  <input {...register(`variants.${index}.color`)} placeholder="Hitam, Cokelat..." className="block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 text-sm outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors" />
                </div>
                <div className="w-24">
                  <label className="block text-[10px] font-bold tracking-widest uppercase text-lightMuted dark:text-darkMuted mb-2">Stok</label>
                  <input type="number" {...register(`variants.${index}.stock`, { valueAsNumber: true })} className="block w-full bg-transparent border border-lightBorder dark:border-darkBorder rounded p-2 text-sm outline-none focus:border-lightMain dark:focus:border-darkMain transition-colors" />
                </div>
                <div>
                  <button type="button" onClick={() => removeVariant(index)} className="border border-red-500 text-red-500 hover:bg-red-500 hover:text-white px-3 py-2 rounded text-[10px] font-bold tracking-widest uppercase transition-colors">
                    Hapus
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      <div>
        <label className="block text-xs font-bold tracking-[0.2em] uppercase text-lightMuted dark:text-darkMuted mb-3">Upload Gambar (Maks 5)</label>
        <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
          {previews.map((previewImg, idx) => (
            <div key={idx} className="relative border border-lightBorder dark:border-darkBorder rounded-lg overflow-hidden aspect-square flex items-center justify-center bg-black/5 dark:bg-white/5">
              {previewImg ? (
                <>
                  <img src={previewImg} alt={`Preview ${idx}`} className="w-full h-full object-cover" />
                  <button type="button" onClick={() => removeImage(idx)} className="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold shadow-md hover:bg-red-600">×</button>
                </>
              ) : (
                <label className="cursor-pointer w-full h-full flex flex-col items-center justify-center text-lightMuted dark:text-darkMuted hover:text-lightMain dark:hover:text-darkMain transition-colors">
                  <span className="text-2xl mb-1">+</span>
                  <span className="text-[10px] font-bold uppercase tracking-widest">Pilih</span>
                  <input type="file" accept="image/*" className="hidden" onChange={(e) => handleImageUpload(e, idx)} />
                </label>
              )}
            </div>
          ))}
        </div>
      </div>

      <button 
        type="submit" 
        disabled={isSubmitting}
        className="w-full bg-lightMain dark:bg-darkMain text-lightBg dark:text-darkBg p-4 rounded-lg hover:opacity-90 disabled:opacity-50 font-bold uppercase tracking-[0.2em] text-xs transition-colors"
      >
        {isSubmitting ? 'Menyimpan...' : 'Simpan Produk'}
      </button>
    </form>
  );
}
