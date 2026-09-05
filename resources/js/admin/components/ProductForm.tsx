import React, { useCallback } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { useDropzone } from 'react-dropzone';
import { toast } from 'sonner';

const productSchema = z.object({
  name: z.string().min(1, "Nama produk wajib diisi"),
  description: z.string().optional(),
  price: z.number().min(0, "Harga minimal 0"),
  stock: z.number().min(0, "Stok minimal 0"),
  category: z.string().optional(),
  new_category: z.string().optional(),
  gender: z.enum(['Pria', 'Wanita', 'Unisex']).optional(),
});

type ProductFormValues = z.infer<typeof productSchema>;

export default function ProductForm() {
  const { register, handleSubmit, formState: { errors, isSubmitting }, setValue } = useForm<ProductFormValues>({
    resolver: zodResolver(productSchema),
  });

  const onDrop = useCallback((acceptedFiles: File[]) => {
    // In real app: upload to server and set image URL
    toast.success(`${acceptedFiles.length} gambar dipilih.`);
    // setValue('image_url', tempUrl)
  }, []);

  const { getRootProps, getInputProps, isDragActive } = useDropzone({ onDrop, accept: { 'image/*': [] } });

  const onSubmit = async (data: ProductFormValues) => {
    try {
      const res = await fetch('/api/admin/products', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
        },
        body: JSON.stringify(data),
      });

      if (!res.ok) throw new Error('Gagal menyimpan');
      
      toast.success('Produk berhasil disimpan!');
      window.location.href = '/admin/products';
    } catch (error) {
      toast.error('Terjadi kesalahan saat menyimpan produk.');
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
          <label className="block text-sm font-medium text-gray-700">Stok</label>
          <input type="number" {...register('stock', { valueAsNumber: true })} className="mt-1 block w-full border rounded p-2" />
          {errors.stock && <p className="text-red-500 text-xs mt-1">{errors.stock.message}</p>}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700">Kategori</label>
          <input {...register('category')} className="mt-1 block w-full border rounded p-2" placeholder="Pilih atau ketik kategori" />
        </div>
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700 mb-2">Upload Gambar (Drag & Drop)</label>
        <div {...getRootProps()} className={`border-2 border-dashed p-10 text-center rounded-lg cursor-pointer ${isDragActive ? 'border-blue-500 bg-blue-50' : 'border-gray-300'}`}>
          <input {...getInputProps()} />
          <p className="text-gray-500">Tarik gambar ke sini, atau klik untuk memilih file</p>
        </div>
      </div>

      <button 
        type="submit" 
        disabled={isSubmitting}
        className="w-full bg-black text-white p-3 rounded-lg hover:bg-gray-800 disabled:opacity-50"
      >
        {isSubmitting ? 'Menyimpan...' : 'Simpan Produk'}
      </button>
    </form>
  );
}
