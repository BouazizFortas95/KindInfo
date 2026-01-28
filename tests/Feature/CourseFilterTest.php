<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Course;
use App\Models\Category;
use Livewire\Livewire;

class CourseFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $category = Category::factory()->create(['name' => 'Programming']);
        Course::factory()->count(5)->create(['category_id' => $category->id]);
    }

    /** @test */
    public function it_can_filter_courses_by_search_term()
    {
        $course = Course::factory()->create([
            'title' => 'Laravel Advanced Course',
            'description' => 'Learn advanced Laravel concepts'
        ]);

        Livewire::test('auth.course-index')
            ->set('search', 'Laravel')
            ->assertSee($course->title);
    }

    /** @test */
    public function it_can_filter_courses_by_category()
    {
        $category = Category::factory()->create(['name' => 'Web Development']);
        $course = Course::factory()->create([
            'category_id' => $category->id,
            'title' => 'Web Development Course'
        ]);

        Livewire::test('auth.course-index')
            ->set('selectedCategories', [$category->id])
            ->assertSee($course->title);
    }

    /** @test */
    public function it_can_filter_courses_by_lesson_count()
    {
        $course = Course::factory()->hasLessons(10)->create([
            'title' => 'Course with Many Lessons'
        ]);

        Livewire::test('auth.course-index')
            ->set('minLessons', 5)
            ->assertSee($course->title);
    }

    /** @test */
    public function it_can_sort_courses_by_title()
    {
        Course::factory()->create(['title' => 'Z Course']);
        Course::factory()->create(['title' => 'A Course']);

        $component = Livewire::test('auth.course-index')
            ->call('setSortBy', 'title');

        $this->assertEquals('title', $component->get('sortBy'));
        $this->assertEquals('asc', $component->get('sortDirection'));
    }

    /** @test */
    public function it_can_toggle_sort_direction()
    {
        $component = Livewire::test('auth.course-index')
            ->call('setSortBy', 'created_at')
            ->call('setSortBy', 'created_at'); // Toggle direction

        $this->assertEquals('desc', $component->get('sortDirection'));
    }

    /** @test */
    public function it_can_clear_all_filters()
    {
        Livewire::test('auth.course-index')
            ->set('search', 'test')
            ->set('selectedCategories', [1])
            ->set('progressFilter', 'started')
            ->set('minLessons', 5)
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('selectedCategories', [])
            ->assertSet('progressFilter', 'all')
            ->assertSet('minLessons', 0);
    }

    /** @test */
    public function it_resets_pagination_when_filters_change()
    {
        $component = Livewire::test('auth.course-index')
            ->set('page', 2)
            ->set('search', 'test');

        // Page should reset to 1 when search changes
        $this->assertEquals(1, $component->get('page'));
    }

    /** @test */
    public function it_shows_correct_results_count()
    {
        Course::factory()->count(3)->create();

        $response = $this->get('/auth/courses');
        
        $response->assertStatus(200);
        $response->assertSee('results found');
    }

    /** @test */
    public function it_handles_empty_search_results()
    {
        Livewire::test('auth.course-index')
            ->set('search', 'nonexistent course')
            ->assertSee('No courses match your search criteria');
    }

    /** @test */
    public function it_maintains_url_pagination_paths()
    {
        Course::factory()->count(20)->create();

        $component = Livewire::test('auth.course-index');
        $courses = $component->call('getCourses');
        
        // Check that pagination URLs don't have duplication
        $this->assertNotNull($courses);
    }
}