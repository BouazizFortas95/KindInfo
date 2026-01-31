<?php

use Livewire\Volt\Component;
use App\Models\Course;
use App\Filament\Auth\Resources\Courses\CourseResource;
use Livewire\WithPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

new class extends Component {
    use WithPagination;

    public array $userProgress = [];
    public int $perPage = 8;
    public string $search = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public array $selectedCategories = [];
    public string $progressFilter = 'all'; // all, started, completed, not_started
    public string $difficultyFilter = 'all';
    public int $minLessons = 0;
    public bool $showFilters = false;
    public string $localeDirection = 'ltr';

    public function mount()
    {
        $this->localeDirection = \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocaleDirection();
        $this->loadUserProgress();
    }

    public function getCourses(): LengthAwarePaginator
    {
        $query = Course::with(['lessons', 'translations', 'category']);

        // Search functionality
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->whereTranslationLike('title', '%' . $this->search . '%')->orWhereTranslationLike('description', '%' . $this->search . '%');
            });
        }

        // Category filter
        if (!empty($this->selectedCategories)) {
            $query->whereIn('category_id', $this->selectedCategories);
        }

        // Lesson count filter
        if ($this->minLessons > 0) {
            $query->has('lessons', '>=', $this->minLessons);
        }

        // Progress filter
        if ($this->progressFilter !== 'all') {
            $courseIds = array_keys($this->userProgress);
            switch ($this->progressFilter) {
                case 'started':
                    $startedCourses = array_filter($this->userProgress, fn($progress) => $progress > 0 && $progress < 100);
                    $query->whereIn('id', array_keys($startedCourses));
                    break;
                case 'completed':
                    $completedCourses = array_filter($this->userProgress, fn($progress) => $progress >= 100);
                    $query->whereIn('id', array_keys($completedCourses));
                    break;
                case 'not_started':
                    $notStartedCourses = array_filter($this->userProgress, fn($progress) => $progress == 0);
                    $query->whereIn('id', array_keys($notStartedCourses));
                    break;
            }
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $courses = $query->paginate($this->perPage, ['*'], 'page', $this->getPage());

        return $courses;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategories()
    {
        $this->resetPage();
    }

    public function updatedProgressFilter()
    {
        $this->resetPage();
    }

    public function updatedMinLessons()
    {
        $this->resetPage();
    }

    public function setSortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedCategories = [];
        $this->progressFilter = 'all';
        $this->difficultyFilter = 'all';
        $this->minLessons = 0;
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function getCategories()
    {
        return \App\Models\Category::with('translations')->get();
    }

    public function viewCourse($courseId)
    {
        $course = Course::findOrFail($courseId);
        return $this->redirect(CourseResource::getUrl('view', ['record' => $course]));
    }

    public function generateFakeThumbnail($course)
    {
        if ($course->thumbnail && !empty($course->thumbnail)) {
            return $course->thumbnail;
        }

        // Generate attractive gradient thumbnails with course info
        $gradients = ['linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)', 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)', 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)', 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)', 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)', 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)', 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)', 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)', 'linear-gradient(135deg, #fad0c4 0%, #ffd1ff 100%)'];

        $gradientIndex = abs(crc32($course->title)) % count($gradients);
        return $gradients[$gradientIndex];
    }

    public function getCourseIcon($course)
    {
        $icons = ['📚', '🎓', '💻', '🔬', '🎨', '📖', '🏆', '⚡', '🚀', '💡', '🎯', '📊', '🔧', '🎪', '🌟', '🎵', '📝', '🔍', '🎭', '🏃'];

        $iconIndex = abs(crc32($course->title)) % count($icons);
        return $icons[$iconIndex];
    }

    private function loadUserProgress()
    {
        // Load user progress from session/database for all courses
        $courses = Course::with('lessons')->get();
        foreach ($courses as $course) {
            $watchedLessons = session()->get('watched_lessons_' . $course->id, []);
            $totalLessons = $course->lessons()->count();

            if ($totalLessons > 0 && count($watchedLessons) > 0) {
                $this->userProgress[$course->id] = (count($watchedLessons) / $totalLessons) * 100;
            } else {
                $this->userProgress[$course->id] = 0;
            }
        }
    }

    public function getCourseProgress($courseId)
    {
        return $this->userProgress[$courseId] ?? 0;
    }

    public function hasStartedCourse($courseId)
    {
        return $this->getCourseProgress($courseId) > 0;
    }
}; ?>

@php
    $currentDir = $localeDirection;
    $isRtl = $currentDir === 'rtl';
@endphp

<div class="premium-courses-wrapper {{ $isRtl ? 'rtl-enforced' : '' }}" dir="{{ $currentDir }}"
    style="direction: {{ $currentDir }} !important;">
    <style>
        .premium-courses-wrapper {
            background: linear-gradient(135deg, rgba(2, 77, 253, 0.192) 0%, rgba(4, 134, 173, 0.274) 50%, rgba(0, 25, 58, 0.123) 100%) !important;
            min-height: 100vh;
            padding: 2rem;
            position: relative;
            z-index: 10;
        }

        /* Reset and override any Filament interference */
        .premium-courses-wrapper * {
            box-sizing: border-box;
        }

        .courses-grid {
            display: grid !important;
            grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
            gap: 2rem !important;
            max-width: 80rem;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            .courses-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (min-width: 1024px) {
            .courses-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }

        @media (min-width: 1280px) {
            .courses-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            }
        }

        .course-card {
            background: #111827 !important;
            border: 1px solid #374151 !important;
            border-radius: 2rem !important;
            overflow: hidden !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            display: block !important;
            width: 100% !important;
        }

        .course-card:hover {
            transform: translateY(-8px) !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.1) !important;
            border-color: #1D4ED8 !important;
        }

        .course-thumbnail {
            transition: all 0.3s ease;
            position: relative;
        }

        .course-card:hover .course-thumbnail {
            transform: scale(1.05);
        }

        .course-thumbnail::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.1);
            transition: opacity 0.3s ease;
        }

        .course-card:hover .course-thumbnail::before {
            opacity: 0;
        }

        .lesson-badge {
            background: rgba(29, 78, 216, 0.9);
            backdrop-filter: blur(8px);
        }

        .progress-bar {
            background: linear-gradient(90deg, #1D4ED8 0%, #3B82F6 100%);
        }

        .cta-button {
            background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 100%);
            transition: all 0.2s ease;
        }

        .cta-button:hover {
            background: linear-gradient(135deg, #1E40AF 0%, #1D4ED8 100%);
            transform: translateY(-1px);
        }

        .empty-state {
            background: rgba(17, 24, 39, 0.6);
            border: 2px dashed #374151;
        }

        .text-truncate-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* Clean Pagination Styles */
        .pagination-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            max-width: 80rem;
            margin: 2rem auto 0 auto;
        }

        .pagination-info {
            order: 2;
        }

        .pagination-info-text {
            font-size: 0.875rem;
            color: #9ca3af;
            text-align: center;
        }

        .pagination-info-text span {
            font-weight: 600;
            color: #d1d5db;
        }

        .pagination-nav {
            order: 1;
        }

        .pagination-list {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pagination-item {
            display: flex;
        }

        .pagination-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            height: 2.5rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            background: rgba(17, 24, 39, 0.6);
            color: #d1d5db;
            border: 1px solid #374151;
        }

        .pagination-link:hover:not(.pagination-disabled):not(.pagination-current) {
            background: rgba(29, 78, 216, 0.6);
            color: white;
            border-color: #3b82f6;
        }

        .pagination-current {
            background: #1d4ed8;
            color: white;
            border-color: #3b82f6;
        }

        .pagination-disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-link button {
            all: unset;
            display: inherit;
            align-items: inherit;
            justify-content: inherit;
            min-width: inherit;
            height: inherit;
            padding: inherit;
            border-radius: inherit;
            font-size: inherit;
            font-weight: inherit;
            text-decoration: inherit;
            transition: inherit;
            background: inherit;
            color: inherit;
            border: inherit;
            cursor: pointer;
            width: 100%;
        }

        .pagination-link[wire\:loading] {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .pagination-separator {
            background: transparent;
            border: none;
            color: #6b7280;
        }

        .pagination-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        /* RTL Support */
        .rtl-enforced {
            direction: rtl !important;
            text-align: right !important;
        }

        .rtl-enforced .sort-controls,
        .rtl-enforced .filter-grid,
        .rtl-enforced .filter-section {
            direction: rtl !important;
        }

        [dir="rtl"] .pagination-list {
            direction: rtl;
        }

        [dir="rtl"] .course-card h3 {
            text-align: right;
        }

        [dir="rtl"] .course-card p {
            text-align: right;
        }

        [dir="rtl"] .pagination-info-text {
            direction: rtl;
            text-align: center;
        }

        /* Enhanced Search and Filter Styles */
        .search-filter-container {
            background: linear-gradient(145deg, rgba(17, 24, 39, 0.95) 0%, rgba(31, 41, 55, 0.9) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 1.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
            max-width: 80rem;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .search-input {
            background: linear-gradient(145deg, rgba(55, 65, 81, 0.8) 0%, rgba(75, 85, 99, 0.6) 100%);
            border: 2px solid rgba(75, 85, 99, 0.6);
            border-radius: 1rem;
            padding: 1rem 1.25rem 1rem 3rem;
            color: white;
            font-size: 0.95rem;
            font-weight: 400;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .search-input:focus {
            outline: none;
            border-color: rgb(59, 130, 246);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15), inset 0 2px 4px rgba(0, 0, 0, 0.2);
            background: linear-gradient(145deg, rgba(59, 130, 246, 0.1) 0%, rgba(75, 85, 99, 0.7) 100%);
        }

        .search-input::placeholder {
            color: rgb(156, 163, 175);
            font-weight: 300;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.25rem;
            height: 1.25rem;
            color: rgb(156, 163, 175);
            transition: color 0.3s ease;
        }

        .search-input:focus+.search-icon {
            color: rgb(96, 165, 250);
        }

        .filter-button {
            background: linear-gradient(135deg, rgba(29, 78, 216, 0.15) 0%, rgba(37, 99, 235, 0.1) 100%);
            border: 2px solid rgba(59, 130, 246, 0.3);
            color: rgb(96, 165, 250);
            border-radius: 1rem;
            padding: 0.875rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            box-shadow: 0 4px 6px rgba(29, 78, 216, 0.1);
        }

        .filter-button:hover {
            background: linear-gradient(135deg, rgba(29, 78, 216, 0.25) 0%, rgba(37, 99, 235, 0.2) 100%);
            border-color: rgb(59, 130, 246);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(29, 78, 216, 0.2);
        }

        .filter-button.active {
            background: linear-gradient(135deg, rgb(29, 78, 216) 0%, rgb(37, 99, 235) 100%);
            border-color: rgb(59, 130, 246);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(29, 78, 216, 0.4);
        }

        .filter-section {
            background: rgba(31, 41, 55, 0.6);
            border: 1px solid rgba(75, 85, 99, 0.5);
            border-radius: 1rem;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .filter-select {
            background: linear-gradient(145deg, rgba(55, 65, 81, 0.8) 0%, rgba(75, 85, 99, 0.6) 100%);
            border: 2px solid rgba(75, 85, 99, 0.6);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            color: white;
            font-size: 0.875rem;
            font-weight: 500;
            min-width: 160px;
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Enhanced styling for multiple select */
        .filter-select[multiple] {
            min-height: 120px;
            max-height: 200px;
            overflow-y: auto;
            padding: 0.5rem;
        }

        .filter-select[multiple] option {
            background: rgba(31, 41, 55, 0.9);
            color: white;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.5rem;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .filter-select[multiple] option:hover {
            background: rgba(29, 78, 216, 0.3);
            border-color: rgba(59, 130, 246, 0.5);
            color: rgb(96, 165, 250);
        }

        .filter-select[multiple] option:checked {
            background: linear-gradient(135deg, rgba(29, 78, 216, 0.8) 0%, rgba(37, 99, 235, 0.75) 100%);
            border-color: rgba(59, 130, 246, 0.6);
            color: white;
            font-weight: 600;
        }

        .filter-select[multiple] option:checked:hover {
            background: linear-gradient(135deg, rgba(29, 78, 216, 0.9) 0%, rgba(37, 99, 235, 0.85) 100%);
        }

        /* Custom scrollbar for multiple select */
        .filter-select[multiple]::-webkit-scrollbar {
            width: 8px;
        }

        .filter-select[multiple]::-webkit-scrollbar-track {
            background: rgba(55, 65, 81, 0.3);
            border-radius: 4px;
        }

        .filter-select[multiple]::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.5);
            border-radius: 4px;
        }

        .filter-select[multiple]::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.7);
        }

        .filter-select:focus {
            outline: none;
            border-color: rgb(59, 130, 246);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15), inset 0 2px 4px rgba(0, 0, 0, 0.2);
            background: linear-gradient(145deg, rgba(59, 130, 246, 0.1) 0%, rgba(75, 85, 99, 0.7) 100%);
        }

        .filter-select option {
            background: rgb(31, 41, 55);
            color: white;
            padding: 0.5rem;
        }

        .filter-input {
            background: linear-gradient(145deg, rgba(55, 65, 81, 0.8) 0%, rgba(75, 85, 99, 0.6) 100%);
            border: 2px solid rgba(75, 85, 99, 0.6);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            color: white;
            font-size: 0.875rem;
            font-weight: 500;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .filter-input:focus {
            outline: none;
            border-color: rgb(59, 130, 246);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15), inset 0 2px 4px rgba(0, 0, 0, 0.2);
            background: linear-gradient(145deg, rgba(59, 130, 246, 0.1) 0%, rgba(75, 85, 99, 0.7) 100%);
        }

        .filter-label {
            display: block;
            color: rgb(229, 231, 235);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .sort-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, rgba(75, 85, 99, 0.4) 0%, rgba(55, 65, 81, 0.6) 100%);
            border: 2px solid rgba(75, 85, 99, 0.6);
            color: rgb(209, 213, 219);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sort-button:hover {
            background: linear-gradient(135deg, rgba(29, 78, 216, 0.2) 0%, rgba(37, 99, 235, 0.15) 100%);
            border-color: rgba(59, 130, 246, 0.8);
            color: rgb(96, 165, 250);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(29, 78, 216, 0.15);
        }

        .sort-button.active {
            background: linear-gradient(135deg, rgb(29, 78, 216) 0%, rgb(37, 99, 235) 100%);
            border-color: rgb(59, 130, 246);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(29, 78, 216, 0.3);
        }

        .results-count {
            color: rgb(156, 163, 175);
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            text-align: center;
            background: rgba(17, 24, 39, 0.6);
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            border: 1px solid rgba(75, 85, 99, 0.3);
            backdrop-filter: blur(10px);
            max-width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }

        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, rgba(29, 78, 216, 0.3) 0%, rgba(37, 99, 235, 0.25) 100%);
            color: rgb(191, 219, 254);
            border: 1px solid rgba(59, 130, 246, 0.4);
            border-radius: 1.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
            box-shadow: 0 2px 8px rgba(29, 78, 216, 0.2);
            transition: all 0.3s ease;
        }

        .filter-chip:hover {
            background: linear-gradient(135deg, rgba(29, 78, 216, 0.4) 0%, rgba(37, 99, 235, 0.35) 100%);
            transform: scale(1.05);
        }

        .filter-chip button {
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            font-size: 1.1rem;
            line-height: 1;
            padding: 0.25rem;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .filter-chip button:hover {
            background: rgba(239, 68, 68, 0.2);
            color: rgb(248, 113, 113);
        }

        .clear-filters-btn {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.1) 100%);
            border: 2px solid rgba(239, 68, 68, 0.3);
            color: rgb(248, 113, 113);
            border-radius: 1rem;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .clear-filters-btn:hover {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.25) 0%, rgba(220, 38, 38, 0.2) 100%);
            border-color: rgb(239, 68, 68);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(239, 68, 68, 0.2);
        }

        @media (max-width: 768px) {
            .search-filter-container {
                padding: 1.5rem;
                border-radius: 1rem;
            }

            .filter-grid {
                grid-template-columns: 1fr !important;
                gap: 1.5rem !important;
            }

            .filter-button {
                width: 100%;
                justify-content: center;
            }

            .sort-controls {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 1rem !important;
            }

            .sort-button {
                width: 100%;
                justify-content: center;
            }
        }

        /* RTL Enhancements */
        [dir="rtl"] .search-input {
            padding: 1rem 3rem 1rem 1.25rem;
        }

        [dir="rtl"] .search-icon {
            left: auto;
            right: 1rem;
        }

        [dir="rtl"] .filter-button svg {
            order: 2;
            margin-right: 0;
            margin-left: 0.5rem;
        }

        [dir="rtl"] .filter-grid {
            direction: rtl !important;
        }

        [dir="rtl"] .filter-label {
            text-align: right !important;
        }

        [dir="rtl"] .filter-select,
        [dir="rtl"] .filter-input {
            text-align: right !important;
        }

        /* Select2 Custom Styling */
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--multiple,
        .select2-container .select2-selection--single {
            background: linear-gradient(145deg, rgba(55, 65, 81, 0.8) 0%, rgba(75, 85, 99, 0.6) 100%) !important;
            border: 2px solid rgba(75, 85, 99, 0.6) !important;
            border-radius: 0.75rem !important;
            min-height: 46px !important;
            padding: 0.25rem 0.5rem !important;
            transition: all 0.3s ease !important;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2) !important;
        }

        .select2-container .select2-selection--single {
            display: flex !important;
            align-items: center !important;
            padding: 0.5rem 1rem !important;
        }

        .select2-container .select2-selection__rendered {
            color: white !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            line-height: 1.5 !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .select2-container .select2-selection__arrow {
            height: 100% !important;
            right: 0.5rem !important;
        }

        .select2-container .select2-selection__arrow b {
            border-color: rgb(156, 163, 175) transparent transparent transparent !important;
            border-style: solid !important;
            border-width: 5px 4px 0 4px !important;
        }

        .select2-container .select2-selection--multiple:focus-within,
        .select2-container .select2-selection--single:focus-within,
        .select2-container.select2-container--open .select2-selection--single,
        .select2-container.select2-container--open .select2-selection--multiple {
            border-color: rgb(59, 130, 246) !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15), inset 0 2px 4px rgba(0, 0, 0, 0.2) !important;
            background: linear-gradient(145deg, rgba(59, 130, 246, 0.1) 0%, rgba(75, 85, 99, 0.7) 100%) !important;
        }

        .select2-container .select2-search--inline .select2-search__field {
            background: transparent !important;
            border: none !important;
            color: white !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            padding: 0.25rem !important;
            outline: none !important;
        }

        .select2-container .select2-search--inline .select2-search__field::placeholder {
            color: rgb(156, 163, 175) !important;
        }

        .select2-container .select2-selection__choice {
            background: linear-gradient(135deg, rgba(29, 78, 216, 0.8) 0%, rgba(37, 99, 235, 0.75) 100%) !important;
            border: 1px solid rgba(59, 130, 246, 0.6) !important;
            border-radius: 1rem !important;
            color: white !important;
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            padding: 0.25rem 0.75rem !important;
            margin: 0.125rem !important;
            box-shadow: 0 2px 4px rgba(29, 78, 216, 0.2) !important;
        }

        .select2-container .select2-selection__choice__remove {
            color: rgba(248, 113, 113, 0.8) !important;
            font-size: 1rem !important;
            font-weight: bold !important;
            margin-right: 0.5rem !important;
            border: none !important;
            background: none !important;
            cursor: pointer !important;
        }

        .select2-container .select2-selection__choice__remove:hover {
            color: rgb(248, 113, 113) !important;
        }

        .select2-dropdown {
            background: rgba(31, 41, 55, 0.95) !important;
            border: 2px solid rgba(59, 130, 246, 0.4) !important;
            border-radius: 0.75rem !important;
            backdrop-filter: blur(20px) !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.2) !important;
            z-index: 1050 !important;
        }

        .select2-container .select2-results>.select2-results__options {
            max-height: 200px !important;
            overflow-y: auto !important;
        }

        .select2-container .select2-results__option {
            background: transparent !important;
            color: rgb(209, 213, 219) !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            padding: 0.75rem 1rem !important;
            transition: all 0.2s ease !important;
            border: none !important;
        }

        .select2-container .select2-results__option:hover,
        .select2-container .select2-results__option--highlighted {
            background: rgba(29, 78, 216, 0.3) !important;
            color: rgb(96, 165, 250) !important;
        }

        .select2-container .select2-results__option[aria-selected="true"] {
            background: rgba(29, 78, 216, 0.5) !important;
            color: white !important;
            font-weight: 600 !important;
        }

        .select2-container .select2-search--dropdown .select2-search__field {
            background: rgba(55, 65, 81, 0.8) !important;
            border: 1px solid rgba(75, 85, 99, 0.6) !important;
            border-radius: 0.5rem !important;
            color: white !important;
            font-size: 0.875rem !important;
            padding: 0.5rem !important;
            margin: 0.5rem !important;
            width: calc(100% - 1rem) !important;
        }

        .select2-container .select2-search--dropdown .select2-search__field:focus {
            border-color: rgb(59, 130, 246) !important;
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
        }

        .select2-container .select2-search--dropdown .select2-search__field::placeholder {
            color: rgb(156, 163, 175) !important;
        }

        /* RTL Support for Select2 */
        [dir="rtl"] .select2-container .select2-selection__choice__remove {
            margin-right: 0 !important;
            margin-left: 0.5rem !important;
        }

        /* Dark scrollbar for dropdown */
        .select2-results__options::-webkit-scrollbar {
            width: 8px;
        }

        .select2-results__options::-webkit-scrollbar-track {
            background: rgba(55, 65, 81, 0.3);
            border-radius: 4px;
        }

        .select2-results__options::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.5);
            border-radius: 4px;
        }

        .select2-results__options::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.7);
        }
    </style>



    <!-- Search and Filter Section -->
    <div class="search-filter-container {{ $isRtl ? 'rtl-enforced' : '' }}">
        <!-- Header -->
        <div style="max-width: 80rem; margin: 0 auto 3rem auto; text-align: center;">
            <h1 style="font-size: 2.25rem; font-weight: bold; color: white; margin-bottom: 1rem;">
                @lang('courses.premium_courses')
            </h1>
            <p style="font-size: 1.125rem; color: #d1d5db; max-width: 42rem; margin: 0 auto;">
                {{ __('courses.courses_subtitle') }}
            </p>
        </div>
        <!-- Search Bar -->
        <div style="position: relative; margin-bottom: 2rem;">
            <input wire:model.live.debounce.300ms="search" type="text" class="search-input"
                placeholder="{{ __('courses.search_courses') }}">
            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        <!-- Filter Toggle and Sort -->
        <div class="sort-controls"
            style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 1.5rem; direction: {{ $localeDirection }} !important;">
            <button wire:click="toggleFilters" class="filter-button {{ $showFilters ? 'active' : '' }}">
                <svg style="width: 1.2rem; height: 1.2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                </svg>
                {{ $showFilters ? __('courses.hide_filters') : __('courses.show_filters') }}
            </button>

            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <span
                    style="color: rgb(229, 231, 235); font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">{{ __('courses.sort_by') }}:</span>
                <div style="display: flex; gap: 0.75rem;">
                    <button wire:click="setSortBy('created_at')"
                        class="sort-button {{ $sortBy === 'created_at' ? 'active' : '' }}">
                        {{ __('courses.sort_newest') }}
                        @if ($sortBy === 'created_at')
                            <svg style="width: 0.8rem; height: 0.8rem;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="{{ $sortDirection === 'asc' ? 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' : 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' }}"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                    </button>
                    <button wire:click="setSortBy('title')"
                        class="sort-button {{ $sortBy === 'title' ? 'active' : '' }}">
                        {{ __('courses.sort_title') }}
                        @if ($sortBy === 'title')
                            <svg style="width: 0.8rem; height: 0.8rem;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="{{ $sortDirection === 'asc' ? 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' : 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' }}"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                    </button>
                </div>
            </div>
        </div>

        <!-- Advanced Filters (Collapsible) -->
        @if ($showFilters)
            <div class="filter-section" wire:key="filter-section-v1"
                style="border-top: 2px solid rgba(59, 130, 246, 0.2); padding-top: 2rem;">
                <div class="filter-grid"
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
                    <!-- Category Filter -->
                    <div>
                        <label class="filter-label">
                            {{ __('courses.categories') }}
                        </label>
                        <div wire:ignore wire:key="category-select-wrapper">
                            <select wire:model.live="selectedCategories" multiple class="filter-select"
                                style="min-height: 120px;">
                                @foreach ($this->getCategories() as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Progress Filter -->
                    <div>
                        <label class="filter-label">
                            {{ __('courses.progress_filter') }}
                        </label>
                        <div wire:ignore wire:key="progress-select-wrapper">
                            <select wire:model.live="progressFilter" class="filter-select">
                                <option value="all">{{ __('courses.all_progress') }}</option>
                                <option value="not_started">{{ __('courses.not_started') }}</option>
                                <option value="started">{{ __('courses.started') }}</option>
                                <option value="completed">{{ __('courses.completed') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Lesson Count Filter -->
                    <div>
                        <label class="filter-label">
                            {{ __('courses.min_lessons') }}
                        </label>
                        <input wire:model.live="minLessons" type="number" min="0" max="50"
                            class="filter-input" placeholder="0">
                    </div>
                </div>

                <!-- Active Filters & Clear Button -->
                <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem; border-top: 1px solid rgba(75, 85, 99, 0.4); padding-top: 1.5rem;">
                    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                        @if (!empty($search))
                            <span class="filter-chip">
                                Search: "{{ $search }}"
                                <button wire:click="$set('search', '')" type="button">×</button>
                            </span>
                        @endif
                        @if (!empty($selectedCategories))
                            <span class="filter-chip">
                                {{ count($selectedCategories) }} {{ __('courses.categories') }}
                                <button wire:click="$set('selectedCategories', [])" type="button">×</button>
                            </span>
                        @endif
                        @if ($progressFilter !== 'all')
                            <span class="filter-chip">
                                {{ __('courses.' . $progressFilter) }}
                                <button wire:click="$set('progressFilter', 'all')" type="button">×</button>
                            </span>
                        @endif
                        @if ($minLessons > 0)
                            <span class="filter-chip">
                                {{ $minLessons }}+ {{ __('courses.lessons') }}
                                <button wire:click="$set('minLessons', 0)" type="button">×</button>
                            </span>
                        @endif
                    </div>

                    @if (!empty($search) || !empty($selectedCategories) || $progressFilter !== 'all' || $minLessons > 0)
                        <button wire:click="clearFilters" class="clear-filters-btn">
                            {{ __('courses.clear_filters') }}
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Course Grid -->
    @php $courses = $this->getCourses(); @endphp

    <!-- Results Counter -->
    <div class="results-count">
        {{ $courses->total() }} {{ __('courses.results_found') }}
    </div>

    @if ($courses->count() > 0)
        <div class="courses-grid">
            @foreach ($courses as $course)
                <div class="course-card">
                    <!-- Thumbnail Container -->
                    <div style="position: relative; aspect-ratio: 16/9; overflow: hidden;">
                        <div style="width: 100%; height: 100%; background: {{ $this->generateFakeThumbnail($course) }}; display: flex; align-items: center; justify-content: center; transition: transform 0.3s ease;"
                            class="course-thumbnail">
                            <!-- Course Icon and Title Overlay -->
                            <div style="text-align: center; color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                                <div
                                    style="font-size: 3rem; margin-bottom: 0.5rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));">
                                    {{ $this->getCourseIcon($course) }}
                                </div>
                                <div
                                    style="font-size: 1rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.9; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $course->title }}
                                </div>
                            </div>
                        </div>

                        <!-- Lesson Count Badge -->
                        <div
                            style="position: absolute; top: 1rem; right: 1rem; background: rgba(29, 78, 216, 0.9); backdrop-filter: blur(8px); padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                            {{ $course->lessons->count() }} {{ __('courses.lessons_count') }}
                        </div>
                    </div>

                    <!-- Card Content -->
                    <div style="padding: 1.5rem;">
                        <!-- Title -->
                        <h3
                            style="font-size: 1.25rem; font-weight: bold; color: white; margin-bottom: 0.75rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $course->title }}
                        </h3>

                        <!-- Description -->
                        <p
                            style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 1.5rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $course->description }}
                        </p>

                        <!-- Progress Bar (if course started) -->
                        @if ($this->hasStartedCourse($course->id))
                            <div style="margin-bottom: 1.5rem;">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span
                                        style="font-size: 0.75rem; color: #9ca3af;">{{ __('courses.progress') }}</span>
                                    <span style="font-size: 0.75rem; color: #60a5fa; font-weight: 600;">
                                        {{ number_format($this->getCourseProgress($course->id), 0) }}%
                                    </span>
                                </div>
                                <div style="width: 100%; background: #374151; border-radius: 9999px; height: 0.5rem;">
                                    <div
                                        style="background: linear-gradient(90deg, #1D4ED8 0%, #3B82F6 100%); height: 0.5rem; border-radius: 9999px; width: {{ $this->getCourseProgress($course->id) }}%; transition: width 0.3s ease;">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- CTA Button -->
                        <button wire:click="viewCourse({{ $course->id }})"
                            style="background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 100%); width: 100%; padding: 0.75rem; border-radius: 9999px; color: white; font-weight: 600; font-size: 0.875rem; border: none; cursor: pointer; transition: all 0.2s ease;"
                            onmouseover="this.style.background='linear-gradient(135deg, #1E40AF 0%, #1D4ED8 100%)'; this.style.transform='translateY(-1px)';"
                            onmouseout="this.style.background='linear-gradient(135deg, #1D4ED8 0%, #2563EB 100%)'; this.style.transform='translateY(0)';">
                            @if ($this->hasStartedCourse($course->id))
                                {{ __('courses.continue_learning') }}
                            @else
                                {{ __('courses.view_course') }}
                            @endif
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-styling-fix">
            {{ $courses->links('pagination::livewire-navigation') }}
        </div>
    @else
        <!-- Empty State -->
        <div style="max-width: 32rem; margin: 0 auto;">
            <div
                style="background: rgba(17, 24, 39, 0.6); border: 2px dashed #374151; border-radius: 2rem; padding: 3rem; text-align: center;">
                <div style="margin-bottom: 1.5rem;">
                    @if (!empty($search) || !empty($selectedCategories) || $progressFilter !== 'all' || $minLessons > 0)
                        <svg style="width: 5rem; height: 5rem; color: #6b7280; margin: 0 auto;" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                            </path>
                        </svg>
                    @else
                        <svg style="width: 5rem; height: 5rem; color: #6b7280; margin: 0 auto;" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z">
                            </path>
                        </svg>
                    @endif
                </div>
                <h3 style="font-size: 1.5rem; font-weight: bold; color: white; margin-bottom: 1rem;">
                    @if (!empty($search) || !empty($selectedCategories) || $progressFilter !== 'all' || $minLessons > 0)
                        {{ __('courses.no_results') }}
                    @else
                        {{ __('courses.no_courses_available') }}
                    @endif
                </h3>
                <p style="color: #9ca3af; font-size: 1.125rem; margin-bottom: 2rem;">
                    @if (!empty($search) || !empty($selectedCategories) || $progressFilter !== 'all' || $minLessons > 0)
                        {{ __('courses.try_different_search') }}
                    @else
                        {{ __('courses.no_courses_message') }}
                    @endif
                </p>

                @if (!empty($search) || !empty($selectedCategories) || $progressFilter !== 'all' || $minLessons > 0)
                    <button wire:click="clearFilters" class="filter-button" style="margin-top: 1rem;">
                        {{ __('courses.clear_filters') }}
                    </button>
                @else
                    <div style="display: flex; justify-content: center; gap: 1rem;">
                        <div
                            style="width: 0.75rem; height: 0.75rem; background: #3b82f6; border-radius: 50%; animation: pulse 1.5s infinite;">
                        </div>
                        <div
                            style="width: 0.75rem; height: 0.75rem; background: #60a5fa; border-radius: 50%; animation: pulse 1.5s infinite; animation-delay: 0.2s;">
                        </div>
                        <div
                            style="width: 0.75rem; height: 0.75rem; background: #93c5fd; border-radius: 50%; animation: pulse 1.5s infinite; animation-delay: 0.4s;">
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Enhanced Select Functionality -->
    <script>
        // Load libraries if not present
        (function() {
            function loadCSS(url) {
                if (!document.querySelector(`link[href="${url}"]`)) {
                    const link = document.createElement('link');
                    link.href = url;
                    link.rel = 'stylesheet';
                    document.head.appendChild(link);
                }
            }

            function loadScript(url, callback) {
                const script = document.createElement('script');
                script.src = url;
                script.onload = callback;
                document.head.appendChild(script);
            }

            // Load Select2 CSS
            loadCSS('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');

            // Load jQuery if not present
            if (typeof jQuery === 'undefined') {
                loadScript('https://code.jquery.com/jquery-3.6.0.min.js', function() {
                    loadSelect2();
                });
            } else {
                loadSelect2();
            }

            function loadSelect2() {
                if (typeof jQuery.fn.select2 === 'undefined') {
                    loadScript('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', function() {
                        setTimeout(initializeEnhancements, 500);
                    });
                } else {
                    setTimeout(initializeEnhancements, 100);
                }
            }

            function initializeEnhancements() {
                // Ensure direction is set on initialization
                setupSelect2();

                // Listen for Livewire updates to reinitialize
                document.addEventListener('livewire:navigated', setupSelect2);
                document.addEventListener('livewire:updated', function() {
                    setTimeout(setupSelect2, 50);
                });

                // Backup observer for dynamic changes
                const observer = new MutationObserver((mutations) => {
                    if (document.querySelector('select[wire\\:model\\.live="selectedCategories"]') &&
                        !document.querySelector('.select2-container')) {
                        setupSelect2();
                    }
                });

                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }

            function setupSelect2() {
                try {
                    const categorySelect = jQuery('select[wire\\:model\\.live="selectedCategories"]');
                    const progressSelect = jQuery('select[wire\\:model\\.live="progressFilter"]');

                    // Destroy existing instances
                    if (categorySelect.hasClass('select2-hidden-accessible')) {
                        categorySelect.select2('destroy');
                    }
                    if (progressSelect.hasClass('select2-hidden-accessible')) {
                        progressSelect.select2('destroy');
                    }

                    // Initialize category select with Select2
                    if (categorySelect.length > 0) {
                        categorySelect.select2({
                            placeholder: '{{ __('courses.select_categories') }}',
                            allowClear: true,
                            multiple: true,
                            closeOnSelect: false,
                            width: '100%',
                            dir: '{{ $localeDirection }}',
                            language: {
                                noResults: function() {
                                    return '{{ __('courses.no_categories_found') }}';
                                },
                                searching: function() {
                                    return '{{ __('courses.searching') }}...';
                                }
                            }
                        }).on('select2:select select2:unselect', function() {
                            // Trigger Livewire update
                            const event = new Event('change', {
                                bubbles: true
                            });
                            this.dispatchEvent(event);
                        });
                    }

                    // Initialize progress select with Select2
                    if (progressSelect.length > 0) {
                        progressSelect.select2({
                            placeholder: '{{ __('courses.select_progress') }}',
                            allowClear: false,
                            width: '100%',
                            dir: '{{ $localeDirection }}',
                            minimumResultsForSearch: Infinity,
                            templateResult: function(option) {
                                if (!option.id) return option.text;

                                const icons = {
                                    'all': '📚',
                                    'not_started': '⏱️',
                                    'started': '▶️',
                                    'completed': '✅'
                                };

                                const icon = icons[option.id] || '';
                                return jQuery('<span><span style="margin-right: 8px;">' + icon + '</span>' +
                                    option.text + '</span>');
                            }
                        }).on('select2:select', function() {
                            // Trigger Livewire update
                            const event = new Event('change', {
                                bubbles: true
                            });
                            this.dispatchEvent(event);
                        });
                    }

                } catch (error) {
                    console.log('Select2 initialization error:', error);
                }
            }
        })();
    </script>

</div>
