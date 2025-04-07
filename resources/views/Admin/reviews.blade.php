@extends('admin.layouts.app')
@section('title', 'Reviews Dashboard')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');
        
        :root {
          --white: #ffffff;
          --gray-50: #f9fafb;
          --gray-100: #f3f4f6;
          --gray-200: #e5e7eb;
          --gray-300: #d1d5db;
          --gray-400: #9ca3af;
          --gray-500: #6b7280;
          --gray-600: #4b5563;
          --gray-700: #374151;
          --gray-800: #1f2937;
          --gray-900: #111827;
          --blue-100: #dbeafe;
          --blue-400: #60a5fa;
          --blue-500: #3b82f6;
          --blue-600: #2563eb;
          --blue-900: #1e3a8a;
          --green-400: #34d399;
          --green-500: #10b981;
          --green-600: #059669;
          --yellow-400: #fbbf24;
          --yellow-500: #f59e0b;
          --yellow-600: #d97706;
          --red-400: #f87171;
          --red-500: #ef4444;
          --red-600: #dc2626;
        }

        .modal-backdrop.show {
            display: none;
        }

        .card-body {
          padding: 0 !important;
          background-color: transparent !important;
        }
        
        #example1_wrapper {
          padding: 0 !important;
        }
        
        .dataTables_wrapper {
          margin: 0 !important;
        }

        .reviews-container {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
          gap: 1.5rem;
          padding: 1rem;
        }

        @media (min-width: 640px) {
          .reviews-container {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
          }
        }

        @media (min-width: 1024px) {
          .reviews-container {
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
          }
        }

        .review-card {
          position: relative;
          background-color: var(--white);
          border-radius: 0.75rem;
          box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
          padding: 1.25rem;
          border: 1px solid var(--gray-200);
          transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .review-card:hover {
          box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
          transform: translateY(-0.25rem);
          border-color: rgba(59, 130, 246, 0.5);
        }

        .review-header {
          display: flex;
          align-items: flex-start;
          justify-content: space-between;
          margin-bottom: 1rem;
        }

        .social-icons {
          display: flex;
          gap: 0.5rem;
        }

        .social-icon {
          width: 2rem;
          height: 2rem;
          padding: 0.375rem;
          border-radius: 50%;
          background-color: var(--gray-100);
          color: var(--gray-600);
          transition: all 0.2s;
        }

        .review-card:hover .social-icon {
          background-color: rgba(59, 130, 246, 0.1);
          color: var(--blue-500);
        }

        .review-date {
          font-size: 0.75rem;
          color: var(--gray-500);
          white-space: nowrap;
        }

        .rating-stars {
          display: flex;
          align-items: center;
          margin-bottom: 0.75rem;
        }

        .star {
          width: 1.25rem;
          height: 1.25rem;
          color: var(--gray-300);
        }

        .star.filled {
          color: var(--yellow-500);
        }

        .rating-value {
          margin-left: 0.5rem;
          font-size: 0.875rem;
          font-weight: 500;
          color: var(--gray-900);
        }

        .review-title {
          font-size: 1.125rem;
          font-weight: 700;
          color: var(--gray-900);
          margin-bottom: 0.75rem;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }

        .rating-bars {
          display: flex;
          flex-direction: column;
          gap: 0.75rem;
          margin-bottom: 1rem;
        }

        .rating-item {
          display: flex;
          align-items: center;
          font-size: 0.875rem;
          gap: 0.5rem;
        }

        .rating-label {
          font-weight: 500;
          color: var(--gray-600);
          width: 5rem;
          flex-shrink: 0;
        }

        .rating-bar-container {
          flex: 1;
          display: flex;
          align-items: center;
          gap: 0.5rem;
          min-width: 0;
        }

        .rating-bar-bg {
          width: 100%;
          height: 0.5rem;
          background-color: var(--gray-100);
          border-radius: 9999px;
          overflow: hidden;
        }

        .rating-bar {
          height: 100%;
          border-radius: 9999px;
        }

        .rating-bar.green { background-color: var(--green-500); }
        .rating-bar.blue { background-color: var(--blue-500); }
        .rating-bar.yellow { background-color: var(--yellow-500); }
        .rating-bar.red { background-color: var(--red-500); }

        .rating-value-text {
          width: 2rem;
          flex-shrink: 0;
          text-align: right;
          font-weight: 500;
        }

        .rating-value-text.green { color: var(--green-600); }
        .rating-value-text.blue { color: var(--blue-600); }
        .rating-value-text.yellow { color: var(--yellow-600); }
        .rating-value-text.red { color: var(--red-600); }

        .review-content {
          position: relative;
          font-size: 0.875rem;
          color: var(--gray-600);
          line-height: 1.5;
          margin-bottom: 0.5rem;
          display: -webkit-box;
          -webkit-line-clamp: 3;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }

        .review-content::after {
          content: '';
          position: absolute;
          bottom: 0;
          left: 0;
          right: 0;
          height: 1.5rem;
          background: linear-gradient(to top, var(--white), transparent);
          opacity: 0.8;
        }

        .review-footer {
          margin-top: 1rem;
          opacity: 0;
          transition: opacity 0.2s;
        }

        .review-card:hover .review-footer {
          opacity: 1;
        }

        .review-link {
          font-size: 0.875rem;
          font-weight: 500;
          color: var(--blue-600);
          text-decoration: none;
        }

        .empty-state {
          text-align: center;
          padding: 3rem 0;
        }

        .empty-icon {
          width: 6rem;
          height: 6rem;
          margin: 0 auto 1rem;
          color: var(--gray-300);
        }

        .empty-title {
          font-size: 1.125rem;
          font-weight: 500;
          color: var(--gray-600);
          margin-bottom: 0.25rem;
        }

        .empty-description {
          color: var(--gray-500);
          max-width: 32rem;
          margin: 0 auto;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
            justify-content: center;
            align-items: center;
        }
        
        .modal-container {
            background-color: white;
            border-radius: 0.5rem;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray-500);
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--gray-200);
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-approve {
            background-color: var(--green-500);
            color: white;
            border: 1px solid var(--green-600);
        }
        
        .btn-approve:hover {
            background-color: var(--green-600);
        }
        
        .btn-reject {
            background-color: var(--red-500);
            color: white;
            border: 1px solid var(--red-600);
        }
        
        .btn-reject:hover {
            background-color: var(--red-600);
        }
        
        .btn-close {
            background-color: var(--gray-200);
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
        }
        
        .btn-close:hover {
            background-color: var(--gray-300);
        }
        
        .full-review-content {
            white-space: pre-line;
            line-height: 1.6;
            color: var(--gray-700);
        }
        
        .cursor-pointer {
            cursor: pointer;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-approved {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .status-rejected {
            background-color: #FEE2E2;
            color: #991B1B;
        }
    </style>

    <!-- Modal Structure -->
    <div class="modal-overlay" id="reviewModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Review Details</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            
            <div id="modalContent">
                <!-- Dynamic content will be inserted here -->
            </div>
            
            <div class="modal-actions">
                <button class="btn btn-approve" id="approveReview">Approve</button>
                <button class="btn btn-reject" id="rejectReview">Reject</button>
                <button class="btn btn-close" id="cancelAction">Close</button>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Customer Reviews</h3>
                        </div>
                        <div class="card-body p-0">
                            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4 p-0 m-0">
                                <div class="row">
                                    <div class="col-sm-12 p-0">
                                        @if($reviews && count($reviews) > 0)
                                            <div class="reviews-container">
                                                @foreach($reviews as $review)
                                                    @php
                                                        $overallRating = (
                                                            ($review['performance'] ?? 0) + 
                                                            ($review['lead'] ?? 0) + 
                                                            ($review['overall_review'] ?? 0)
                                                        ) / 3;
                                                        $roundedRating = round($overallRating);
                                                        $formattedDate = isset($review['created_at']) 
                                                            ? date('M j, Y', strtotime($review['created_at'])) 
                                                            : 'Date not available';
                                                        $statusClass = 'status-pending';
                                                        if(isset($review['status'])) {
                                                            if($review['status'] === 'approved') $statusClass = 'status-approved';
                                                            if($review['status'] === 'rejected') $statusClass = 'status-rejected';
                                                        }
                                                    @endphp
                                                    
                                                    <div class="review-card cursor-pointer" data-id="{{ $review['id'] }}" onclick="openReviewModal({{ json_encode($review) }})">
                                                        <div class="review-header">
                                                            <div class="social-icons">
                                                                @foreach($review['social_media'] ?? [] as $platform)
                                                                    @if($platform['platform'] === 'tiktok')
                                                                        <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                                                                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                                                        </svg>
                                                                    @elseif($platform['platform'] === 'facebook')
                                                                        <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                                                                            <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                                                                        </svg>
                                                                    @elseif($platform['platform'] === 'instagram')
                                                                        <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                                                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                                                        </svg>
                                                                    @elseif($platform['platform'] === 'x')
                                                                        <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                                                                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                                                        </svg>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                            <div>
                                                                <span class="review-date">{{ $formattedDate }}</span>
                                                                @if(isset($review['status']))
                                                                    <span class="status-badge {{ $statusClass }} ml-2">{{ $review['status'] }}</span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="rating-stars">
                                                            <div class="flex">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <svg class="star {{ $i <= $roundedRating ? 'filled' : '' }}" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                    </svg>
                                                                @endfor
                                                            </div>
                                                            <span class="rating-value">{{ number_format($overallRating, 1) }}</span>
                                                        </div>

                                                        <h3 class="review-title">
                                                            {{ $review['title'] ?? "No title provided" }}
                                                        </h3>
                                                        
                                                        <div class="rating-bars">
                                                            <div class="rating-item">
                                                                <span class="rating-label">Quality:</span>
                                                                <div class="rating-bar-container">
                                                                    <div class="rating-bar-bg">
                                                                        <div class="rating-bar {{ $review['performance'] >= 4 ? 'green' : ($review['performance'] >= 3 ? 'blue' : ($review['performance'] >= 2 ? 'yellow' : 'red')) }}" 
                                                                            style="width: {{ ($review['performance'] / 5) * 100 }}%"></div>
                                                                    </div>
                                                                    <span class="rating-value-text {{ $review['performance'] >= 4 ? 'green' : ($review['performance'] >= 3 ? 'blue' : ($review['performance'] >= 2 ? 'yellow' : 'red')) }}">
                                                                        {{ number_format($review['performance'] ?? 0, 1) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="rating-item">
                                                                <span class="rating-label">Value:</span>
                                                                <div class="rating-bar-container">
                                                                    <div class="rating-bar-bg">
                                                                        <div class="rating-bar {{ $review['lead'] >= 4 ? 'green' : ($review['lead'] >= 3 ? 'blue' : ($review['lead'] >= 2 ? 'yellow' : 'red')) }}" 
                                                                            style="width: {{ ($review['lead'] / 5) * 100 }}%"></div>
                                                                    </div>
                                                                    <span class="rating-value-text {{ $review['lead'] >= 4 ? 'green' : ($review['lead'] >= 3 ? 'blue' : ($review['lead'] >= 2 ? 'yellow' : 'red')) }}">
                                                                        {{ number_format($review['lead'] ?? 0, 1) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="rating-item">
                                                                <span class="rating-label">Support:</span>
                                                                <div class="rating-bar-container">
                                                                    <div class="rating-bar-bg">
                                                                        <div class="rating-bar {{ $review['overall_review'] >= 4 ? 'green' : ($review['overall_review'] >= 3 ? 'blue' : ($review['overall_review'] >= 2 ? 'yellow' : 'red')) }}" 
                                                                            style="width: {{ ($review['overall_review'] / 5) * 100 }}%"></div>
                                                                    </div>
                                                                    <span class="rating-value-text {{ $review['overall_review'] >= 4 ? 'green' : ($review['overall_review'] >= 3 ? 'blue' : ($review['overall_review'] >= 2 ? 'yellow' : 'red')) }}">
                                                                        {{ number_format($review['overall_review'] ?? 0, 1) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="relative">
                                                            <p class="review-content">
                                                                "{{ $review['review'] ?? 'No review content provided' }}"
                                                            </p>
                                                        </div>

                                                        <div class="review-footer">
                                                            <a href="#" class="review-link">View full review →</a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="empty-state">
                                                <div class="empty-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <h3 class="empty-title">No reviews yet</h3>
                                                <p class="empty-description">
                                                    You haven't received any reviews yet. Check back later or share your services to get feedback.
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
@endsection

@push('js')
<script>
    function openReviewModal(review) {
        currentReviewId = review.id;
        
        // Populate modal content
        const modalContent = $('#modalContent');
        modalContent.html(`
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-start">
                    <h4 class="font-weight-bold text-lg mb-2">${review.title || 'No title provided'}</h4>
                    <span class="badge ${getStatusClass(review.status)}">${review.status || 'pending'}</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    ${Array.from({length: 5}, (_, i) => 
                        `<svg class="star ${i < Math.round((review.performance + review.lead + review.overall_review) / 3) ? 'filled' : ''}" 
                              viewBox="0 0 20 20" fill="currentColor" width="1.25rem" height="1.25rem">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>`
                    ).join('')}
                    <span class="ml-2">${((review.performance + review.lead + review.overall_review) / 3).toFixed(1)}</span>
                </div>
                <p class="full-review-content">${review.review || 'No review content provided'}</p>
            </div>
            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <label class="form-label">Quality Rating</label>
                    <div class="d-flex align-items-center">
                        <div class="progress w-100">
                            <div class="progress-bar bg-${getRatingColor(review.performance)}" 
                                 style="width: ${(review.performance / 5) * 100}%"></div>
                        </div>
                        <span class="ml-2 text-${getRatingColor(review.performance)} font-weight-bold">
                            ${review.performance.toFixed(1)}
                        </span>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Value Rating</label>
                    <div class="d-flex align-items-center">
                        <div class="progress w-100">
                            <div class="progress-bar bg-${getRatingColor(review.lead)}" 
                                 style="width: ${(review.lead / 5) * 100}%"></div>
                        </div>
                        <span class="ml-2 text-${getRatingColor(review.lead)} font-weight-bold">
                            ${review.lead.toFixed(1)}
                        </span>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Support Rating</label>
                    <div class="d-flex align-items-center">
                        <div class="progress w-100">
                            <div class="progress-bar bg-${getRatingColor(review.overall_review)}" 
                                 style="width: ${(review.overall_review / 5) * 100}%"></div>
                        </div>
                        <span class="ml-2 text-${getRatingColor(review.overall_review)} font-weight-bold">
                            ${review.overall_review.toFixed(1)}
                        </span>
                    </div>
                </div>
            </div>
            ${review.status === 'rejected' && review.rejection_reason ? `
            <div class="alert alert-danger mt-4">
                <h5 class="alert-heading">Rejection Reason</h5>
                <p>${review.rejection_reason}</p>
            </div>
            ` : ''}
            <div class="mt-4" id="rejectionSection" style="${review.status === 'rejected' ? 'display: none;' : ''}">
                <label class="form-label">Rejection Reason</label>
                <textarea id="rejectionReason" rows="3" class="form-control" 
                          placeholder="Optional reason for rejection"></textarea>
            </div>
        `);
        
        // Show/hide buttons based on current status
        const approveBtn = $('#approveReview');
        const rejectBtn = $('#rejectReview');
        
        if (review.status === 'approved') {
            approveBtn.hide();
            rejectBtn.text('Revert to Pending');
        } else if (review.status === 'rejected') {
            rejectBtn.hide();
            approveBtn.text('Revert to Pending');
        } else {
            approveBtn.show().text('Approve');
            rejectBtn.show().text('Reject');
        }
        
        // Show modal
        $('#reviewModal').modal({
            backdrop: 'static',
            keyboard: false
        }).css({
            'display': 'flex',
            'align-items': 'center'
        });
        
        // Adjust modal dialog position
        $('.modal-dialog').css({
            'margin': '0 auto',
            'max-height': '90vh',
            'overflow-y': 'auto'
        });
    }
    function getRatingColor(rating) {
        if (rating >= 4) return 'success';
        if (rating >= 3) return 'primary';
        if (rating >= 2) return 'warning';
        return 'danger';
    }
        
    function getStatusClass(status) {
        if (status === 'approved') return 'badge-success';
        if (status === 'rejected') return 'badge-danger';
        return 'badge-warning';
    }

    //$(document).ready(function() {
        let currentReviewId = null;
        // Close modal
        $('#closeModal, #cancelAction').on('click', function() {
            $('#reviewModal').modal('hide');
        });
        
        // Approve review
        $('#approveReview').on('click', function() {
            if (!currentReviewId) return;
            
            const isReverting = $(this).text().includes('Revert');
            const confirmMessage = isReverting 
                ? 'Are you sure you want to revert this review to pending status?'
                : 'Are you sure you want to approve this review?';
            
            if (!confirm(confirmMessage)) {
                return;
            }
            
            $.ajax({
                url: `/projects/vivek/influencers/admin/reviews/${currentReviewId}/approve`,
                method: 'POST',
                data: JSON.stringify({ is_reverting: isReverting }),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    if (data.success) {
                        alert('Review status updated successfully');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to update review status'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('An error occurred while updating the review status');
                }
            });
        });
        
        // Reject review
        $('#rejectReview').on('click', function() {
            if (!currentReviewId) return;
            
            const reason = $('#rejectionReason').val();
            const isReverting = $(this).text().includes('Revert');
            
            const confirmMessage = isReverting 
                ? 'Are you sure you want to revert this review to pending status?'
                : 'Are you sure you want to reject this review?';
            
            if (!confirm(confirmMessage)) {
                return;
            }
            
            $.ajax({
                url: `/projects/vivek/influencers/admin/reviews/${currentReviewId}/reject`,
                method: 'POST',
                data: JSON.stringify({ reason, is_reverting: isReverting }),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    if (data.success) {
                        alert('Review status updated successfully');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to update review status'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('An error occurred while updating the review status');
                }
            });
        });

        // Close modal when clicking outside
        $('#reviewModal').on('click', function(e) {
            if (e.target === this) {
                $(this).modal('hide');
            }
        });

        // Add keyboard support for modal
        $(document).on('keydown', function(e) {
            if ($('#reviewModal').hasClass('show') && e.key === 'Escape') {
                $('#reviewModal').modal('hide');
            }
        });
    //});
</script>
@endpush