import React from 'react';
import { createRoot } from 'react-dom/client';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import ProductTable from './components/ProductTable';
import ProductForm from './components/ProductForm';
import NewsletterTable from './components/NewsletterTable';
import SalesDashboard from './components/SalesDashboard';
import OrderTable from './components/OrderTable';
import AdminRevamp from './components/AdminRevamp';
import { Toaster } from 'sonner';

const queryClient = new QueryClient();

// Fungsi helper untuk mount komponen ke dalam elemen DOM
function mountComponent(elementId: string, Component: React.FC) {
  const element = document.getElementById(elementId);
  if (element) {
    const root = createRoot(element);
    root.render(
      <QueryClientProvider client={queryClient}>
        <Component />
        <Toaster position="top-right" richColors />
      </QueryClientProvider>
    );
  }
}

// Mount komponen sesuai dengan halaman yang dibuka
document.addEventListener('DOMContentLoaded', () => {
  mountComponent('react-product-table', ProductTable);
  mountComponent('react-product-form', ProductForm);
  mountComponent('react-newsletter-table', NewsletterTable);
  mountComponent('react-sales-dashboard', SalesDashboard);
  mountComponent('react-order-table', OrderTable);
  mountComponent('react-admin-revamp', AdminRevamp);
});
