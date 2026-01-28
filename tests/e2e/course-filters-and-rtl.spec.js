const { test, expect } = require('@playwright/test');

test.describe('Course Platform - Filters and RTL Layout', () => {
  
  test.describe('Enhanced Filter Styling', () => {
    test('should display modern filter design with proper styling', async ({ page }) => {
      await page.goto('/auth/courses');
      
      // Check search filter container has enhanced styling
      const filterContainer = page.locator('.search-filter-container');
      await expect(filterContainer).toBeVisible();
      
      // Verify gradient background and modern design
      await expect(filterContainer).toHaveCSS('background', /linear-gradient/);
      await expect(filterContainer).toHaveCSS('border-radius', '1.5rem');
      await expect(filterContainer).toHaveCSS('backdrop-filter', 'blur(20px)');
    });

    test('should show enhanced search input with proper styling', async ({ page }) => {
      await page.goto('/auth/courses');
      
      const searchInput = page.locator('.search-input');
      await expect(searchInput).toBeVisible();
      
      // Check enhanced input styling
      await expect(searchInput).toHaveCSS('border-radius', '1rem');
      await expect(searchInput).toHaveCSS('background', /linear-gradient/);
      
      // Test search functionality
      await searchInput.fill('test course');
      await expect(searchInput).toHaveValue('test course');
    });

    test('should display enhanced filter button with hover effects', async ({ page }) => {
      await page.goto('/auth/courses');
      
      const filterButton = page.locator('.filter-button');
      await expect(filterButton).toBeVisible();
      
      // Check initial styling
      await expect(filterButton).toHaveCSS('border-radius', '1rem');
      await expect(filterButton).toHaveCSS('background', /linear-gradient/);
      
      // Test hover effect
      await filterButton.hover();
      
      // Click to expand filters
      await filterButton.click();
      await expect(filterButton).toHaveClass(/active/);
    });

    test('should show enhanced filter section when expanded', async ({ page }) => {
      await page.goto('/auth/courses');
      
      // Expand filters
      await page.locator('.filter-button').click();
      
      // Check filter section styling
      const filterSection = page.locator('.filter-section');
      await expect(filterSection).toBeVisible();
      await expect(filterSection).toHaveCSS('border-radius', '1rem');
      await expect(filterSection).toHaveCSS('backdrop-filter', 'blur(10px)');
      
      // Check filter inputs styling
      const filterSelect = page.locator('.filter-select').first();
      await expect(filterSelect).toBeVisible();
      await expect(filterSelect).toHaveCSS('border-radius', '0.75rem');
    });

    test('should display enhanced filter chips with proper interactions', async ({ page }) => {
      await page.goto('/auth/courses');
      
      // Add a search filter to create a chip
      await page.locator('.search-input').fill('test');
      
      // Expand filters and set a progress filter
      await page.locator('.filter-button').click();
      await page.locator('select[wire\\:model\\.live="progressFilter"]').selectOption('started');
      
      // Check filter chips
      const filterChips = page.locator('.filter-chip');
      await expect(filterChips).toBeVisible();
      
      // Check chip styling
      await expect(filterChips.first()).toHaveCSS('border-radius', '1.5rem');
      await expect(filterChips.first()).toHaveCSS('background', /linear-gradient/);
      
      // Test chip removal
      await filterChips.first().locator('button').click();
    });

    test('should show enhanced clear filters button', async ({ page }) => {
      await page.goto('/auth/courses');
      
      // Add filters to show clear button
      await page.locator('.search-input').fill('test');
      await page.locator('.filter-button').click();
      
      const clearButton = page.locator('.clear-filters-btn');
      await expect(clearButton).toBeVisible();
      await expect(clearButton).toHaveCSS('border-radius', '1rem');
      await expect(clearButton).toHaveCSS('color', 'rgb(248, 113, 113)');
      
      // Test clear functionality
      await clearButton.click();
      await expect(page.locator('.search-input')).toHaveValue('');
    });

    test('should be responsive on mobile devices', async ({ page }) => {
      // Set mobile viewport
      await page.setViewportSize({ width: 375, height: 667 });
      await page.goto('/auth/courses');
      
      const filterContainer = page.locator('.search-filter-container');
      await expect(filterContainer).toBeVisible();
      
      // Check mobile-specific styling
      await expect(filterContainer).toHaveCSS('padding', '1.5rem');
      
      // Check filter button responsiveness
      const filterButton = page.locator('.filter-button');
      await filterButton.click();
      
      // On mobile, filter grid should be single column
      const filterGrid = page.locator('.filter-grid');
      await expect(filterGrid).toBeVisible();
    });
  });

  test.describe('RTL Layout Consistency', () => {
    test('should maintain consistent RTL sidebar position when switching lessons', async ({ page }) => {
      // Switch to Arabic language first
      await page.goto('/');
      
      // Look for language switcher and select Arabic
      try {
        const langSwitcher = page.locator('[href*="setlocale/ar"]');
        if (await langSwitcher.isVisible()) {
          await langSwitcher.click();
        }
      } catch (e) {
        // If language switcher not found, continue with test
        console.log('Language switcher not found, continuing...');
      }
      
      // Navigate to course player
      await page.goto('/auth/courses/60'); // Assuming course ID 60 exists
      
      // Check RTL direction
      const body = page.locator('body');
      const direction = await body.getAttribute('dir') || 
                      await page.locator('[dir]').first().getAttribute('dir');
      
      if (direction === 'rtl') {
        // Verify sidebar is on the left for RTL
        const sidebar = page.locator('.sidebar-container');
        await expect(sidebar).toBeVisible();
        
        const mainArea = page.locator('.main-video-area');
        await expect(mainArea).toBeVisible();
        
        // In RTL, sidebar should have order: 1, main area should have order: 2
        await expect(sidebar).toHaveCSS('order', '1');
        await expect(mainArea).toHaveCSS('order', '2');
        
        // Test lesson switching - click on second lesson
        const secondLesson = page.locator('.lesson-item').nth(1);
        if (await secondLesson.isVisible()) {
          await secondLesson.click();
          
          // Verify sidebar position remains consistent
          await expect(sidebar).toHaveCSS('order', '1');
          await expect(mainArea).toHaveCSS('order', '2');
        }
      }
    });

    test('should have proper RTL lesson item layout', async ({ page }) => {
      await page.goto('/auth/courses/60');
      
      // Check if page has RTL direction
      const direction = await page.locator('[dir]').first().getAttribute('dir');
      
      if (direction === 'rtl') {
        const lessonItems = page.locator('.lesson-item');
        await expect(lessonItems.first()).toBeVisible();
        
        // Check RTL-specific styling for lesson items
        const lessonContent = page.locator('.lesson-item-content').first();
        await expect(lessonContent).toHaveCSS('flex-direction', 'row-reverse');
        
        // Check lesson number container positioning
        const numberContainer = page.locator('.lesson-number-container').first();
        await expect(numberContainer).toHaveCSS('order', '2');
        
        // Check lesson details text alignment
        const lessonDetails = page.locator('.lesson-details').first();
        await expect(lessonDetails).toHaveCSS('text-align', 'right');
      }
    });

    test('should maintain RTL layout consistency across lesson changes', async ({ page }) => {
      await page.goto('/auth/courses/60');
      
      const direction = await page.locator('[dir]').first().getAttribute('dir');
      
      if (direction === 'rtl') {
        const gridContainer = page.locator('.grid-container');
        
        // Check initial grid layout
        await expect(gridContainer).toHaveCSS('grid-template-columns', '1fr 3fr');
        
        // Click on different lessons and verify layout remains consistent
        const lessons = page.locator('.lesson-item');
        const lessonCount = await lessons.count();
        
        for (let i = 0; i < Math.min(3, lessonCount); i++) {
          await lessons.nth(i).click();
          await page.waitForTimeout(500); // Allow for any animations
          
          // Verify grid layout remains consistent
          await expect(gridContainer).toHaveCSS('grid-template-columns', '1fr 3fr');
          
          // Verify sidebar order remains consistent
          const sidebar = page.locator('.sidebar-container');
          await expect(sidebar).toHaveCSS('order', '1');
        }
      }
    });

    test('should have proper RTL search icon position', async ({ page }) => {
      await page.goto('/auth/courses');
      
      const direction = await page.locator('[dir]').first().getAttribute('dir');
      
      if (direction === 'rtl') {
        const searchIcon = page.locator('.search-icon');
        await expect(searchIcon).toBeVisible();
        
        // In RTL, search icon should be positioned on the right
        await expect(searchIcon).toHaveCSS('right', '1rem');
        
        // Search input padding should also be adjusted for RTL
        const searchInput = page.locator('.search-input');
        await expect(searchInput).toHaveCSS('padding', '1rem 3rem 1rem 1.25rem');
      }
    });

    test('should handle RTL video controls layout', async ({ page }) => {
      await page.goto('/auth/courses/60');
      
      const direction = await page.locator('[dir]').first().getAttribute('dir');
      
      if (direction === 'rtl') {
        // Check if video controls exist and have proper RTL styling
        const videoControls = page.locator('.video-controls');
        if (await videoControls.isVisible()) {
          const controlsDiv = videoControls.locator('> div');
          await expect(controlsDiv).toHaveCSS('flex-direction', 'row-reverse');
        }
        
        // Check setting items RTL layout
        const settingItems = page.locator('.setting-item');
        if (await settingItems.first().isVisible()) {
          await expect(settingItems.first()).toHaveCSS('flex-direction', 'row-reverse');
        }
      }
    });
  });

  test.describe('Cross-browser Compatibility', () => {
    ['chromium', 'firefox'].forEach(browserName => {
      test(`should work correctly in ${browserName}`, async ({ page, browserName: currentBrowser }) => {
        test.skip(currentBrowser !== browserName, `This test is only for ${browserName}`);
        
        await page.goto('/auth/courses');
        
        // Test basic filter functionality
        const filterButton = page.locator('.filter-button');
        await expect(filterButton).toBeVisible();
        await filterButton.click();
        
        // Test search functionality
        const searchInput = page.locator('.search-input');
        await searchInput.fill('test');
        await expect(searchInput).toHaveValue('test');
        
        // Test filter styling consistency across browsers
        await expect(filterButton).toHaveCSS('border-radius', '1rem');
      });
    });
  });
});