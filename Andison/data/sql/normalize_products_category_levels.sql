-- Normalize product hierarchy assignment in Supabase
-- Run this in Supabase SQL Editor.

begin;

alter table if exists public.products
    add column if not exists sub_subcategory_id text;

-- Backfill legacy rows where subcategory_id actually contains a sub-subcategory id.
update public.products p
set
    sub_subcategory_id = p.subcategory_id,
    subcategory_id = ss.subcategory_id
from public.sub_subcategories ss
where p.sub_subcategory_id is null
  and p.subcategory_id = ss.id;

-- Normalize empty strings to NULL for consistency.
update public.products
set sub_subcategory_id = null
where sub_subcategory_id = '';

-- Helpful indexes for category-page queries.
create index if not exists idx_products_category_sub on public.products(category_id, subcategory_id);
create index if not exists idx_products_category_subsub on public.products(category_id, sub_subcategory_id);

commit;
