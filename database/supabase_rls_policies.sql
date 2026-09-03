-- ============================================================
-- ENY LEATHER WEB STORE — Supabase/PostgreSQL Row Level Security
-- ============================================================
-- Jalankan SQL ini di Supabase SQL Editor setelah migrasi.
-- Prasyarat: Supabase Auth sudah dikonfigurasi, user.id
-- sinkron dengan auth.uid().
-- ============================================================

-- ============================================
-- HELPER FUNCTION: Get current user role
-- ============================================
CREATE OR REPLACE FUNCTION public.get_user_role()
RETURNS TEXT AS $$
  SELECT role FROM public.users WHERE id = (auth.uid())::bigint;
$$ LANGUAGE SQL SECURITY DEFINER STABLE;

-- ============================================
-- ENABLE RLS ON ALL TABLES
-- ============================================
ALTER TABLE public.orders ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.order_items ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.payments ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.ratings ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.payment_methods ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.audit_logs ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.products ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.users ENABLE ROW LEVEL SECURITY;

-- ============================================
-- USERS TABLE POLICIES
-- ============================================

-- Semua authenticated user bisa melihat profil sendiri
CREATE POLICY "users_view_own_profile" ON public.users
    FOR SELECT TO authenticated
    USING (id = (auth.uid())::bigint OR public.get_user_role() IN ('admin', 'superadmin'));

-- User bisa update profil sendiri
CREATE POLICY "users_update_own_profile" ON public.users
    FOR UPDATE TO authenticated
    USING (id = (auth.uid())::bigint)
    WITH CHECK (id = (auth.uid())::bigint);

-- Superadmin bisa kelola semua user
CREATE POLICY "superadmin_manage_users" ON public.users
    FOR ALL TO authenticated
    USING (public.get_user_role() = 'superadmin');

-- ============================================
-- PRODUCTS TABLE POLICIES
-- ============================================

-- Semua orang (termasuk anon) bisa melihat produk aktif
CREATE POLICY "public_view_products" ON public.products
    FOR SELECT TO anon, authenticated
    USING (deleted_at IS NULL);

-- Admin & Superadmin bisa kelola produk
CREATE POLICY "admin_manage_products" ON public.products
    FOR ALL TO authenticated
    USING (public.get_user_role() IN ('admin', 'superadmin'));

-- ============================================
-- ORDERS TABLE POLICIES
-- ============================================

-- Customer: hanya bisa melihat pesanannya sendiri
CREATE POLICY "customers_view_own_orders" ON public.orders
    FOR SELECT TO authenticated
    USING (
        user_id = (auth.uid())::bigint
        OR public.get_user_role() IN ('admin', 'superadmin')
    );

-- Customer: hanya bisa membuat pesanan untuk dirinya sendiri
CREATE POLICY "customers_create_own_orders" ON public.orders
    FOR INSERT TO authenticated
    WITH CHECK (user_id = (auth.uid())::bigint);

-- Customer: bisa update pesanan sendiri (confirm received, dll)
CREATE POLICY "customers_update_own_orders" ON public.orders
    FOR UPDATE TO authenticated
    USING (
        user_id = (auth.uid())::bigint
        OR public.get_user_role() IN ('admin', 'superadmin')
    );

-- Admin/Superadmin: full access ke semua pesanan
CREATE POLICY "admin_full_access_orders" ON public.orders
    FOR ALL TO authenticated
    USING (public.get_user_role() IN ('admin', 'superadmin'));

-- ============================================
-- ORDER ITEMS TABLE POLICIES
-- ============================================

-- Customer bisa melihat item dari pesanannya sendiri
CREATE POLICY "customers_view_own_order_items" ON public.order_items
    FOR SELECT TO authenticated
    USING (
        order_id IN (SELECT id FROM public.orders WHERE user_id = (auth.uid())::bigint)
        OR public.get_user_role() IN ('admin', 'superadmin')
    );

-- Customer bisa membuat item untuk pesanannya sendiri
CREATE POLICY "customers_create_own_order_items" ON public.order_items
    FOR INSERT TO authenticated
    WITH CHECK (
        order_id IN (SELECT id FROM public.orders WHERE user_id = (auth.uid())::bigint)
    );

-- Admin/Superadmin: full access
CREATE POLICY "admin_full_access_order_items" ON public.order_items
    FOR ALL TO authenticated
    USING (public.get_user_role() IN ('admin', 'superadmin'));

-- ============================================
-- PAYMENTS TABLE POLICIES
-- ============================================

-- Customer: bisa melihat & membuat pembayaran untuk pesanannya sendiri
CREATE POLICY "customers_own_payments_select" ON public.payments
    FOR SELECT TO authenticated
    USING (
        order_id IN (SELECT id FROM public.orders WHERE user_id = (auth.uid())::bigint)
        OR public.get_user_role() IN ('admin', 'superadmin')
    );

CREATE POLICY "customers_create_payments" ON public.payments
    FOR INSERT TO authenticated
    WITH CHECK (
        order_id IN (SELECT id FROM public.orders WHERE user_id = (auth.uid())::bigint)
    );

-- Admin/Superadmin: full access (verifikasi, reject, dll)
CREATE POLICY "admin_full_access_payments" ON public.payments
    FOR ALL TO authenticated
    USING (public.get_user_role() IN ('admin', 'superadmin'));

-- ============================================
-- RATINGS TABLE POLICIES
-- ============================================

-- Publik: semua orang bisa melihat rating yang visible
CREATE POLICY "public_view_visible_ratings" ON public.ratings
    FOR SELECT TO anon, authenticated
    USING (is_visible = TRUE AND deleted_at IS NULL);

-- Customer: hanya bisa membuat rating untuk pesanannya yang sudah selesai
CREATE POLICY "customers_create_rating" ON public.ratings
    FOR INSERT TO authenticated
    WITH CHECK (
        user_id = (auth.uid())::bigint
        AND order_id IN (
            SELECT id FROM public.orders 
            WHERE user_id = (auth.uid())::bigint 
            AND status = 'completed'
        )
    );

-- Admin: bisa melihat semua rating (termasuk hidden)
CREATE POLICY "admin_view_all_ratings" ON public.ratings
    FOR SELECT TO authenticated
    USING (public.get_user_role() IN ('admin', 'superadmin'));

-- Superadmin: bisa mengelola (soft delete/update) semua rating
CREATE POLICY "superadmin_manage_ratings" ON public.ratings
    FOR ALL TO authenticated
    USING (public.get_user_role() = 'superadmin');

-- ============================================
-- PAYMENT METHODS TABLE POLICIES
-- ============================================

-- Semua orang bisa melihat metode pembayaran aktif
CREATE POLICY "public_view_active_payment_methods" ON public.payment_methods
    FOR SELECT TO anon, authenticated
    USING (is_active = TRUE);

-- Superadmin: bisa kelola semua metode pembayaran
CREATE POLICY "superadmin_manage_payment_methods" ON public.payment_methods
    FOR ALL TO authenticated
    USING (public.get_user_role() = 'superadmin');

-- ============================================
-- AUDIT LOGS TABLE POLICIES
-- ============================================

-- Hanya Superadmin yang bisa melihat audit logs
CREATE POLICY "superadmin_view_audit_logs" ON public.audit_logs
    FOR SELECT TO authenticated
    USING (public.get_user_role() = 'superadmin');

-- Admin & Superadmin bisa menulis log
CREATE POLICY "admin_write_audit_logs" ON public.audit_logs
    FOR INSERT TO authenticated
    WITH CHECK (public.get_user_role() IN ('admin', 'superadmin'));
