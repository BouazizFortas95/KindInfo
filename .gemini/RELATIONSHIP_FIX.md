# Relationship Search Fix - DONE ✅

## The Problem
You encountered a SQL error `no such column: categories.name` when trying to select a parent category. This happened because the `name` field is stored in the `category_translations` table, but Filament was trying to find it directly in the main `categories` table.

## The Fix
Updated `app/Filament/Resources/Categories/CategoryResource.php`:

1. **Relation Search**: Updated the `parent_id` field. Instead of a simple relationship, I added a `modifyQueryUsing` closure that joins the `category_translations` table. This makes the `name` column available to the SQL search query.
2. **Table Search**: Updated the table columns (`name` and `parent.name`). Added custom search logic using the `whereTranslationLike` scope from the Astrotomic library. This allows you to search for categories by their translated names in the table as well.

## Result
You can now search and select parent categories without any SQL errors. The search will correctly look through the translations for the currently active language.

**Status: FIXED** 🛠️
