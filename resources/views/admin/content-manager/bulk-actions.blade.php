@extends('layouts.admin')
@section('content')
<div class="container">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{__('Bulk Actions')}}</h3>
                <p class="text-sm text-gray-500 mt-1">{{__('Perform actions on multiple content items at once')}}</p>
            </div>

            <!-- Content Selection -->
            <div class="mb-6">
                <div class="flex flex-wrap gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{__('Content Type')}}</label>
                        <select id="content-type" class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-gray-800 dark:text-gray-300">
                            <option value="all">{{__('All Content')}}</option>
                            <option value="movie">{{__('Movies')}}</option>
                            <option value="tv">{{__('TV Shows')}}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{__('Status')}}</label>
                        <select id="status-filter" class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-gray-800 dark:text-gray-300">
                            <option value="">{{__('All Statuses')}}</option>
                            <option value="publish">{{__('Published')}}</option>
                            <option value="draft">{{__('Draft')}}</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button id="load-content" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            {{__('Load Content')}}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content List -->
            <div id="content-list" class="mb-6 hidden">
                <div class="flex items-center justify-between mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="select-all" class="mr-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{__('Select All')}}</span>
                    </label>
                    <span id="selected-count" class="text-sm text-gray-500">{{__('0 items selected')}}</span>
                </div>
                
                <div id="content-items" class="max-h-96 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <!-- Content items will be loaded here -->
                </div>
            </div>

            <!-- Actions -->
            <div id="bulk-actions" class="hidden">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{__('Actions')}}</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <!-- Status Actions -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-3">{{__('Status')}}</h5>
                        <div class="space-y-2">
                            <button class="bulk-action w-full text-left px-3 py-2 text-sm bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded hover:bg-green-200 dark:hover:bg-green-900/50" 
                                    data-action="publish">
                                {{__('Publish Selected')}}
                            </button>
                            <button class="bulk-action w-full text-left px-3 py-2 text-sm bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 rounded hover:bg-yellow-200 dark:hover:bg-yellow-900/50" 
                                    data-action="draft">
                                {{__('Mark as Draft')}}
                            </button>
                        </div>
                    </div>

                    <!-- Feature Actions -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-3">{{__('Featured')}}</h5>
                        <div class="space-y-2">
                            <button class="bulk-action w-full text-left px-3 py-2 text-sm bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded hover:bg-blue-200 dark:hover:bg-blue-900/50" 
                                    data-action="feature">
                                {{__('Add to Featured')}}
                            </button>
                            <button class="bulk-action w-full text-left px-3 py-2 text-sm bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600" 
                                    data-action="unfeature">
                                {{__('Remove from Featured')}}
                            </button>
                        </div>
                    </div>

                    <!-- Danger Actions -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-3">{{__('Danger Zone')}}</h5>
                        <div class="space-y-2">
                            <button class="bulk-action w-full text-left px-3 py-2 text-sm bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded hover:bg-red-200 dark:hover:bg-red-900/50" 
                                    data-action="delete" 
                                    onclick="return confirm('{{__('Are you sure you want to delete selected items? This action cannot be undone.')}}')">
                                {{__('Delete Selected')}}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="flex items-center justify-between">
                        <a href="{{route('admin.content-manager.index')}}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <x-ui.icon name="arrow-left" class="w-4 h-4 mr-2"/>
                            {{__('Back to Content Manager')}}
                        </a>
                        
                        <div id="action-progress" class="hidden flex items-center gap-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-600 border-t-transparent"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{__('Processing...')}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadContentBtn = document.getElementById('load-content');
    const contentList = document.getElementById('content-list');
    const contentItems = document.getElementById('content-items');
    const bulkActions = document.getElementById('bulk-actions');
    const selectAll = document.getElementById('select-all');
    const selectedCount = document.getElementById('selected-count');
    
    let selectedItems = new Set();

    loadContentBtn.addEventListener('click', loadContent);
    selectAll.addEventListener('change', toggleSelectAll);

    async function loadContent() {
        const contentType = document.getElementById('content-type').value;
        const statusFilter = document.getElementById('status-filter').value;
        
        try {
            const response = await fetch(`{{route('admin.content-manager.search')}}?type=${contentType}&status=${statusFilter}`);
            const data = await response.json();
            
            displayContent(data);
            contentList.classList.remove('hidden');
            bulkActions.classList.remove('hidden');
        } catch (error) {
            console.error('Error loading content:', error);
            alert('{{__('Error loading content. Please try again.')}}');
        }
    }

    function displayContent(items) {
        contentItems.innerHTML = '';
        
        items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'flex items-center gap-3 p-3 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800';
            
            div.innerHTML = `
                <label class="flex items-center">
                    <input type="checkbox" class="content-item" value="${item.id}" onchange="updateSelection()">
                </label>
                <img src="${item.image || '/static/img/placeholder.png'}" alt="${item.title}" class="w-12 h-16 object-cover rounded">
                <div class="flex-1 min-w-0">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 truncate">${item.title}</h4>
                    <p class="text-sm text-gray-500">${item.type.charAt(0).toUpperCase() + item.type.slice(1)} • ${item.status}</p>
                </div>
            `;
            
            contentItems.appendChild(div);
        });
    }

    function toggleSelectAll() {
        const checkboxes = document.querySelectorAll('.content-item');
        checkboxes.forEach(cb => {
            cb.checked = selectAll.checked;
        });
        updateSelection();
    }

    window.updateSelection = function() {
        const checkboxes = document.querySelectorAll('.content-item:checked');
        selectedItems.clear();
        checkboxes.forEach(cb => selectedItems.add(cb.value));
        
        selectedCount.textContent = `${selectedItems.size} {{__('items selected')}}`;
        selectAll.checked = checkboxes.length > 0 && checkboxes.length === document.querySelectorAll('.content-item').length;
    }

    // Bulk action handlers
    document.querySelectorAll('.bulk-action').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (selectedItems.size === 0) {
                alert('{{__('Please select items first.')}}');
                return;
            }

            const action = this.dataset.action;
            const actionProgress = document.getElementById('action-progress');
            
            try {
                actionProgress.classList.remove('hidden');
                
                const response = await fetch('{{route('admin.content-manager.process-bulk-action')}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        action: action,
                        content_ids: Array.from(selectedItems)
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    loadContent(); // Reload content
                    selectedItems.clear();
                    updateSelection();
                } else {
                    alert(result.message || '{{__('Error processing action.')}}');
                }
            } catch (error) {
                console.error('Error processing bulk action:', error);
                alert('{{__('Error processing action. Please try again.')}}');
            } finally {
                actionProgress.classList.add('hidden');
            }
        });
    });
});
</script>
@endsection