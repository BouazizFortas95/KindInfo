<?php

use Livewire\Volt\Component;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;

new class extends Component
{
    public Course $course;
    public $activeLesson;
    public $watchedLessons = [];
    public $lessonProgress = [];
    public $autoPlay = true;
    public $playbackSpeed = 1.0;
    public $theaterMode = false;
    public $currentTime = 0;
    public $duration = 0;

    public function mount(Course $course)
    {
        $this->course = $course->load(['lessons.translations', 'translations']);
        $this->activeLesson = $this->course->lessons()->with('translations')->first();
        $this->loadUserProgress();
    }

    public function selectLesson($id)
    {
        $this->saveCurrentProgress();
        $this->activeLesson = $this->course->lessons()->with('translations')->find($id);
        $this->dispatch('lesson-changed', [
            'lessonId' => $id,
            'videoUrl' => $this->activeLesson->video_url,
            'autoPlay' => $this->autoPlay
        ]);
    }

    public function toggleAutoPlay()
    {
        $this->autoPlay = !$this->autoPlay;
        $this->dispatch('autoplay-changed', $this->autoPlay);
    }

    public function setPlaybackSpeed($speed)
    {
        $this->playbackSpeed = $speed;
        $this->dispatch('speed-changed', $speed);
    }

    public function toggleTheaterMode()
    {
        $this->theaterMode = !$this->theaterMode;
    }

    public function markLessonComplete($lessonId)
    {
        if (!in_array($lessonId, $this->watchedLessons)) {
            $this->watchedLessons[] = $lessonId;
        }
        $this->lessonProgress[$lessonId] = 100;

        if ($this->autoPlay) {
            $this->playNextLesson();
        }
    }

    public function updateProgress($lessonId, $currentTime, $duration)
    {
        $this->currentTime = $currentTime;
        $this->duration = $duration;

        if ($duration > 0) {
            $progress = ($currentTime / $duration) * 100;
            $this->lessonProgress[$lessonId] = min($progress, 100);

            if ($progress >= 90) {
                $this->markLessonComplete($lessonId);
            }
        }
    }

    public function playNextLesson()
    {
        $lessons = $this->course->lessons;
        $currentIndex = $lessons->search(fn($lesson) => $lesson->id === $this->activeLesson->id);

        if ($currentIndex !== false && $currentIndex < $lessons->count() - 1) {
            $nextLesson = $lessons[$currentIndex + 1];
            $this->selectLesson($nextLesson->id);
        }
    }

    public function playPreviousLesson()
    {
        $lessons = $this->course->lessons;
        $currentIndex = $lessons->search(fn($lesson) => $lesson->id === $this->activeLesson->id);

        if ($currentIndex !== false && $currentIndex > 0) {
            $previousLesson = $lessons[$currentIndex - 1];
            $this->selectLesson($previousLesson->id);
        }
    }

    private function loadUserProgress()
    {
        // Simulate loading progress from session/database
        $this->watchedLessons = session()->get('watched_lessons_' . $this->course->id, []);
        $this->lessonProgress = session()->get('lesson_progress_' . $this->course->id, []);
    }

    private function saveCurrentProgress()
    {
        // Save progress to session/database
        session()->put('watched_lessons_' . $this->course->id, $this->watchedLessons);
        session()->put('lesson_progress_' . $this->course->id, $this->lessonProgress);
    }

    public function getLessonProgress($lessonId)
    {
        return $this->lessonProgress[$lessonId] ?? 0;
    }

    public function isLessonCompleted($lessonId)
    {
        return in_array($lessonId, $this->watchedLessons);
    }
}; ?>

<div style="background: linear-gradient(135deg, rgba(2, 77, 253, 0.192) 0%, rgba(4, 134, 173, 0.274) 50%, rgba(0, 25, 58, 0.123) 10%); min-height: 100vh;" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
    <style>
        .video-container {
            background: #000;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            aspect-ratio: 16/9;
            border: 1px solid rgb(31, 41, 55);
            position: relative;
        }

        .theater-mode {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 9999;
            background: #000;
            border-radius: 0;
        }

        .video-controls {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            padding: 1rem;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .video-container:hover .video-controls {
            opacity: 1;
        }

        .control-button {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            padding: 0.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            margin: 0 0.25rem;
        }

        .control-button:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .progress-bar {
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
            margin: 0.5rem 0;
            cursor: pointer;
        }

        .progress-fill {
            height: 100%;
            background: rgb(59, 130, 246);
            border-radius: 2px;
            transition: width 0.1s;
        }

        .lesson-info {
            background: rgba(17, 24, 39, 0.8);
            backdrop-filter: blur(16px);
            border-radius: 1rem;
            border: 1px solid rgb(31, 41, 55);
            padding: 1.5rem;
        }

        .sidebar-container {
            background: rgba(17, 24, 39, 0.9);
            backdrop-filter: blur(16px);
            border-radius: 1rem;
            border: 1px solid rgb(31, 41, 55);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            position: sticky;
            top: 1.5rem;
        }

        .sidebar-header {
            background: linear-gradient(to right, rgb(37, 99, 235), rgb(147, 51, 234));
            padding: 1rem;
        }

        .lesson-item {
            width: 100%;
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid rgb(31, 41, 55);
            transition: all 0.2s;
            border: none;
            background: transparent;
            cursor: pointer;
            position: relative;
        }

        .lesson-item:hover {
            background: rgba(31, 41, 55, 0.5);
        }

        .lesson-item.active {
            background: rgba(37, 99, 235, 0.2);
            border-left: 4px solid rgb(59, 130, 246);
        }

        .lesson-item.completed {
            background: rgba(34, 197, 94, 0.1);
            border-left: 4px solid rgb(34, 197, 94);
        }

        .lesson-progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 2px;
            background: rgb(59, 130, 246);
            transition: width 0.3s;
        }

        .lesson-number {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            position: relative;
        }

        .lesson-number.active {
            background: rgb(37, 99, 235);
            color: white;
        }

        .lesson-number.completed {
            background: rgb(34, 197, 94);
            color: white;
        }

        .lesson-number.inactive {
            background: rgb(55, 65, 81);
            color: rgb(156, 163, 175);
        }

        .lesson-item:hover .lesson-number.inactive {
            background: rgb(37, 99, 235);
            color: white;
        }

        .completion-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 12px;
            height: 12px;
            background: rgb(34, 197, 94);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .attachment-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: linear-gradient(to right, rgb(37, 99, 235), rgb(29, 78, 216));
            color: white;
            border-radius: 9999px;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        }

        .attachment-button:hover {
            background: linear-gradient(to right, rgb(29, 78, 216), rgb(37, 99, 235));
            transform: scale(1.05);
        }

        .settings-panel {
            background: rgba(17, 24, 39, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgb(31, 41, 55);
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1rem;
        }

        .setting-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgb(31, 41, 55);
        }

        .setting-item:last-child {
            border-bottom: none;
        }

        .toggle-switch {
            position: relative;
            width: 44px;
            height: 24px;
            background: rgb(55, 65, 81);
            border-radius: 12px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .toggle-switch.active {
            background: rgb(59, 130, 246);
        }

        .toggle-slider {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        .toggle-switch.active .toggle-slider {
            transform: translateX(20px);
        }

        @media (min-width: 1024px) {
            .grid-container {
                grid-template-columns: 3fr 1fr !important;
            }
        }

        .theater-mode .grid-container {
            grid-template-columns: 1fr !important;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Enhanced RTL Support */
        /* [dir="rtl"] .lesson-item {
            text-align: right;
        } */

        /* [dir="rtl"] .lesson-item > div {
            flex-direction: row-reverse !important;
        } */

        /* [dir="rtl"] .lesson-number {
            order: 2;
            margin-left: 0;
            margin-right: 0.75rem;
        } */

        /* [dir="rtl"] .lesson-details {
            order: 1;
        }

        [dir="rtl"] .video-controls > div {
            flex-direction: row-reverse;
        } */

        [dir="rtl"] .grid-container {
            grid-template-columns: 1fr 3fr !important;
        }

        [dir="rtl"] .sidebar-container {
            order: 1;
        }

        [dir="rtl"] .main-video-area {
            order: 2;
        }

        [dir="rtl"] .setting-item {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .attachment-button {
            flex-direction: row-reverse;
        }

        /* Ensure consistent layout regardless of content changes */
        .grid-container {
            display: grid !important;
            gap: 1.5rem !important;
        }

        @media (min-width: 1024px) {
            .grid-container {
                grid-template-columns: 3fr 1fr !important;
            }

            /* [dir="rtl"] .grid-container {
                grid-template-columns: 1fr 3fr !important;
            } */
        }

        /* Force layout consistency
        .sidebar-container {
            order: 2;
        }

        .main-video-area {
            order: 1;
        } */

        /* [dir="rtl"] .sidebar-container {
            order: 1 !important;
        }

        [dir="rtl"] .main-video-area {
            order: 2 !important;
        } */

        /* [dir="rtl"] .setting-item {
            flex-direction: row-reverse;
        } */
    </style>

    <div style="padding: 1.5rem;">
        <div class="grid-container">
            <!-- Main Video Area -->
            <div class="main-video-area" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Video Player -->
                <div class="video-container {{ $theaterMode ? 'theater-mode' : '' }}" style="position: relative;">
                    @if($activeLesson && $activeLesson->video_url)
                        <iframe id="video-player"
                                src="{{ str_replace('watch?v=', 'embed/', $activeLesson->video_url) }}?enablejsapi=1&autoplay={{ $autoPlay ? 1 : 0 }}"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;"
                                allowfullscreen></iframe>

                        <!-- Enhanced Video Controls -->
                        <div class="video-controls">
                            <!-- Progress Bar -->
                            <div class="progress-bar" onclick="seekVideo(event)">
                                <div class="progress-fill" style="width: {{ $duration > 0 ? ($currentTime / $duration * 100) : 0 }}%"></div>
                            </div>

                            <!-- Control Buttons -->
                            <div style="display: flex; align-items: center; justify-content: between; width: 100%;">
                                <!-- Left Controls -->
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <button wire:click="playPreviousLesson" class="control-button" title="{{ __('courses.previous_lesson') }}">
                                        <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/>
                                        </svg>
                                    </button>

                                    <button wire:click="playNextLesson" class="control-button" title="{{ __('courses.next_lesson') }}">
                                        <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Center Progress Time -->
                                <div style="color: white; font-size: 0.875rem;">
                                    {{ gmdate('H:i:s', $currentTime) }} / {{ gmdate('H:i:s', $duration) }}
                                </div>

                                <!-- Right Controls -->
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <!-- Playback Speed -->
                                    <select wire:change="setPlaybackSpeed($event.target.value)" class="control-button" style="background: rgba(255, 255, 255, 0.1); border: none; color: white;">
                                        <option value="0.5" {{ $playbackSpeed == 0.5 ? 'selected' : '' }}>0.5x</option>
                                        <option value="0.75" {{ $playbackSpeed == 0.75 ? 'selected' : '' }}>0.75x</option>
                                        <option value="1" {{ $playbackSpeed == 1 ? 'selected' : '' }}>1x</option>
                                        <option value="1.25" {{ $playbackSpeed == 1.25 ? 'selected' : '' }}>1.25x</option>
                                        <option value="1.5" {{ $playbackSpeed == 1.5 ? 'selected' : '' }}>1.5x</option>
                                        <option value="2" {{ $playbackSpeed == 2 ? 'selected' : '' }}>2x</option>
                                    </select>

                                    <button wire:click="toggleTheaterMode" class="control-button" title="{{ __('courses.theater_mode') }}">
                                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                            <div style="text-align: center;">
                                <svg style="width: 4rem; height: 4rem; margin: 0 auto 1rem auto; color: rgb(107, 114, 128);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.5a1.5 1.5 0 011.5 1.5M12 5a7 7 0 717 7 7 7 0 01-7 7 7 7 0 01-7-7 7 7 0 017-7z" />
                                </svg>
                                <p style="color: rgb(156, 163, 175); font-size: 1.125rem;">{{ __('courses.select_lesson_to_begin') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Settings Panel -->
                @if(!$theaterMode)
                    <div class="settings-panel">
                        <h4 style="color: white; font-weight: bold; margin-bottom: 1rem;">{{ __('courses.player_settings') }}</h4>

                        <div class="setting-item">
                            <span style="color: rgb(209, 213, 219);">{{ __('courses.auto_play_next_lesson') }}</span>
                            <div wire:click="toggleAutoPlay" class="toggle-switch {{ $autoPlay ? 'active' : '' }}">
                                <div class="toggle-slider"></div>
                            </div>
                        </div>

                        <div class="setting-item">
                            <span style="color: rgb(209, 213, 219);">{{ __('courses.playback_speed') }}</span>
                            <span style="color: white; font-weight: 500;">{{ $playbackSpeed }}x</span>
                        </div>

                        <div class="setting-item">
                            <span style="color: rgb(209, 213, 219);">{{ __('courses.course_progress') }}</span>
                            <span style="color: rgb(34, 197, 94); font-weight: 500;">
                                {{ count($watchedLessons) }}/{{ $course->lessons->count() }} {{ __('courses.completed') }}
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Video Information & Attachments -->
                <div class="lesson-info">
                    <div>
                        <h1 style="font-size: 2rem; font-weight: bold; color: white; margin-bottom: 0.5rem;">
                            {{ $activeLesson->title ?? 'Welcome to the Course' }}
                        </h1>

                        @if($activeLesson && $activeLesson->description)
                            <p style="color: rgb(209, 213, 219); font-size: 1.125rem; line-height: 1.75;">
                                {{ $activeLesson->description }}
                            </p>
                        @endif
                    </div>

                    <!-- Attachments -->
                    @if($activeLesson && !empty($activeLesson->attachments))
                        <div style="border-top: 1px solid rgb(31, 41, 55); padding-top: 1.5rem; margin-top: 1.5rem;">
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: white; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                <svg style="width: 1.25rem; height: 1.25rem; color: rgb(96, 165, 250);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                {{ __('courses.course_resources') }}
                            </h3>

                            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                                @foreach($activeLesson->attachments as $file)
                                    <a href="{{ Storage::url($file) }}" target="_blank" class="attachment-button">
                                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ basename($file) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="sidebar-container">
                <!-- Sidebar Header -->
                <div class="sidebar-header">
                    <h3 style="font-weight: bold; color: white; display: flex; align-items: center; gap: 0.5rem; font-size: 1.125rem;">
                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        {{ __('courses.course_curriculum') }}
                    </h3>
                    <p style="color: rgb(191, 219, 254); font-size: 0.875rem; margin-top: 0.25rem;">
                        {{ $course->lessons->count() }} {{ __('courses.lessons') }}
                    </p>
                </div>

                <!-- Lessons List -->
                <div style="max-height: 37.5rem; overflow-y: auto;">
                    @foreach($course->lessons as $index => $lesson)
                        @php
                            $isActive = $activeLesson && $activeLesson->id === $lesson->id;
                            $isCompleted = $this->isLessonCompleted($lesson->id);
                            $progress = $this->getLessonProgress($lesson->id);
                        @endphp

                        <button
                            wire:click="selectLesson({{ $lesson->id }})"
                            class="lesson-item {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}">

                            <!-- Progress Bar -->
                            <div class="lesson-progress-bar" style="width: {{ $progress }}%"></div>

                            <div class="lesson-item-content" style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                <!-- Lesson Number & Play Icon -->
                                <div class="lesson-number-container" style="flex-shrink: 0; position: relative;">
                                    @if($isActive)
                                        <div class="lesson-number active">
                                            <svg style="width: 1.25rem; height: 1.25rem;" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </div>
                                        <div style="position: absolute; top: -0.25rem; right: -0.25rem;">
                                            <div style="width: 0.75rem; height: 0.75rem; background: rgb(74, 222, 128); border-radius: 50%; animation: pulse 2s infinite;"></div>
                                        </div>
                                    @elseif($isCompleted)
                                        <div class="lesson-number completed">
                                            <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    @else
                                        <div class="lesson-number inactive">
                                            {{ $index + 1 }}
                                        </div>
                                    @endif

                                    @if($isCompleted && !$isActive)
                                        <div class="completion-badge">
                                            <svg style="width: 8px; height: 8px;" fill="white" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Lesson Details -->
                                <div style="flex: 1; min-width: 0;">
                                    <h4 style="font-weight: 600; font-size: 0.875rem; line-height: 1.25; margin-bottom: 0.25rem;
                                              color: {{ $isActive ? 'rgb(96, 165, 250)' : ($isCompleted ? 'rgb(34, 197, 94)' : 'rgb(229, 231, 235)') }};">
                                        {{ $lesson->title }}
                                    </h4>

                                    @if($isActive)
                                        <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.75rem;">
                                            <span style="color: rgb(74, 222, 128); font-weight: 500;">● {{ strtoupper(__('courses.playing_now')) }}</span>
                                        </div>
                                    @elseif($isCompleted)
                                        <div style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.75rem;">
                                            <span style="color: rgb(34, 197, 94); font-weight: 500;">✓ {{ strtoupper(__('courses.completed')) }}</span>
                                        </div>
                                    @else
                                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; color: rgb(107, 114, 128);">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                @if(!empty($lesson->attachments))
                                                    <span style="display: flex; align-items: center; gap: 0.25rem;">
                                                        <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                        </svg>
                                                        {{ count($lesson->attachments) }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if($progress > 0 && $progress < 100)
                                                <span style="color: rgb(59, 130, 246); font-weight: 500;">{{ round($progress) }}%</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced JavaScript for Video Controls and Progress Tracking -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let player = null;
            let progressInterval = null;

            // Keyboard Shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

                switch(e.code) {
                    case 'Space':
                        e.preventDefault();
                        togglePlayPause();
                        break;
                    case 'ArrowLeft':
                        e.preventDefault();
                        seekRelative(-10);
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        seekRelative(10);
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        changeSpeed(0.25);
                        break;
                    case 'ArrowDown':
                        e.preventDefault();
                        changeSpeed(-0.25);
                        break;
                    case 'KeyF':
                        e.preventDefault();
                        @this.call('toggleTheaterMode');
                        break;
                    case 'KeyN':
                        e.preventDefault();
                        @this.call('playNextLesson');
                        break;
                    case 'KeyP':
                        e.preventDefault();
                        @this.call('playPreviousLesson');
                        break;
                }
            });

            // Video Progress Tracking
            function startProgressTracking() {
                if (progressInterval) clearInterval(progressInterval);

                progressInterval = setInterval(function() {
                    const iframe = document.getElementById('video-player');
                    if (iframe && iframe.contentWindow) {
                        // Simulate progress tracking (would need YouTube API for real implementation)
                        const currentTime = Math.random() * 300; // Mock current time
                        const duration = 300; // Mock duration

                        @this.call('updateProgress', @this.activeLesson.id, currentTime, duration);
                    }
                }, 5000); // Update every 5 seconds
            }

            // Video Control Functions
            function togglePlayPause() {
                // Would interact with YouTube API
                console.log('Toggle play/pause');
            }

            function seekRelative(seconds) {
                // Would interact with YouTube API
                console.log('Seek', seconds, 'seconds');
            }

            function changeSpeed(increment) {
                const currentSpeed = @this.playbackSpeed;
                const newSpeed = Math.max(0.25, Math.min(2, currentSpeed + increment));
                @this.call('setPlaybackSpeed', newSpeed);
            }

            function seekVideo(event) {
                const progressBar = event.currentTarget;
                const rect = progressBar.getBoundingClientRect();
                const percent = (event.clientX - rect.left) / rect.width;

                // Would seek to percent * duration
                console.log('Seek to', Math.round(percent * 100), '%');
            }

            // Livewire Event Listeners
            Livewire.on('lesson-changed', function(data) {
                console.log('Lesson changed:', data);
                startProgressTracking();

                // Update iframe src if needed
                const iframe = document.getElementById('video-player');
                if (iframe && data.videoUrl) {
                    const autoplay = data.autoPlay ? '&autoplay=1' : '';
                    iframe.src = data.videoUrl.replace('watch?v=', 'embed/') + '?enablejsapi=1' + autoplay;
                }
            });

            Livewire.on('autoplay-changed', function(autoPlay) {
                console.log('Autoplay changed:', autoPlay);
            });

            Livewire.on('speed-changed', function(speed) {
                console.log('Speed changed:', speed);
                // Would change YouTube player speed
            });

            // Initialize progress tracking
            startProgressTracking();

            // Auto-complete lesson when reaching 90%
            window.markLessonComplete = function(lessonId) {
                @this.call('markLessonComplete', lessonId);
            };

            // Course completion celebration
            Livewire.on('course-completed', function() {
                // Show celebration animation
                showCompletionCelebration();
            });

            function showCompletionCelebration() {
                // Create confetti effect or celebration modal
                console.log('🎉 Course completed! Congratulations!');
            }
        });

        // Theater mode escape key handler
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && @this.theaterMode) {
                @this.call('toggleTheaterMode');
            }
        });
    </script>
</div>
