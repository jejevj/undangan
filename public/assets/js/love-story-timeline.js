/**
 * Love Story Timeline AJAX Handler
 */
var LoveStoryTimeline = (function() {
    'use strict';

    var invitationId = null;

    // Initialize
    function init(id) {
        invitationId = id;
        console.log('Love Story Timeline initialized for invitation:', invitationId);
        
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bindEvents);
        } else {
            bindEvents();
        }
    }

    // Bind all events
    function bindEvents() {
        console.log('Binding Love Story Timeline events...');
        
        // Add Timeline Form - using vanilla JS
        var addForm = document.getElementById('addTimelineForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Add timeline form submitted via vanilla JS');
                handleAddTimeline(this);
                return false;
            });
            console.log('Add form event bound');
        } else {
            console.error('Add timeline form not found!');
        }

        // Edit Timeline Form - using vanilla JS
        var editForm = document.getElementById('editTimelineForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Edit timeline form submitted via vanilla JS');
                handleEditTimeline(this);
                return false;
            });
            console.log('Edit form event bound');
        } else {
            console.error('Edit timeline form not found!');
        }
    }

    // Handle Add Timeline - using vanilla JS fetch
    function handleAddTimeline(form) {
        console.log('handleAddTimeline called');
        
        var isTimeskip = document.getElementById('addIsTimeskip').checked;
        var csrfToken = form.querySelector('[name="_token"]').value;
        
        var data = {
            _token: csrfToken
        };
        
        if (isTimeskip) {
            // Timeskip mode - send timeskip fields only
            data.is_timeskip = 1;
            data.timeskip_label = document.getElementById('addTimeskipLabelInput').value || '';
            data.message = document.getElementById('addTimeskipMessage').value || ''; // Optional description
            data.sender = 'groom'; // Default sender for timeskip
        } else {
            // Normal mode - send normal fields only
            data.is_timeskip = 0;
            data.sender = document.getElementById('addSender').value;
            data.message = document.getElementById('addMessage').value;
            data.event_date = document.getElementById('addEventDate').value || null;
            data.event_time = document.getElementById('addEventTime').value || null;
        }

        console.log('Sending data:', data);

        // Show loading state
        var submitBtn = form.querySelector('button[type="submit"]');
        var originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';

        fetch('/dash/invitations/' + invitationId + '/love-story', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': data._token
            },
            body: JSON.stringify(data)
        })
        .then(function(response) {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(function(result) {
            console.log('Success response:', result);
            
            // Reset button
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            
            if (result.success) {
                // Close modal
                var modalEl = document.getElementById('addTimelineModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
                
                // Reset form
                form.reset();
                
                // Add to DOM
                addTimelineItemToDOM(result.timeline);
                
                // Re-initialize sortable if function exists
                if (typeof window.initTimelineSortable === 'function') {
                    window.initTimelineSortable();
                }
                
                // Show success
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: result.message
                });
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            
            // Reset button
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menyimpan timeline.'
            });
        });
    }

    // Handle Edit Timeline - using vanilla JS fetch
    function handleEditTimeline(form) {
        console.log('handleEditTimeline called');
        
        var timelineId = document.getElementById('editTimelineId').value;
        var isTimeskip = document.getElementById('editIsTimeskip').checked;
        var csrfToken = form.querySelector('[name="_token"]').value;
        
        var data = {
            _token: csrfToken,
            _method: 'PUT'
        };
        
        if (isTimeskip) {
            // Timeskip mode - send timeskip fields only
            data.is_timeskip = 1;
            data.timeskip_label = document.getElementById('editTimeskipLabelInput').value || '';
            data.message = document.getElementById('editTimeskipMessage').value || '';
            data.sender = 'groom';
        } else {
            // Normal mode - send normal fields only
            data.is_timeskip = 0;
            data.sender = document.getElementById('editSender').value;
            data.message = document.getElementById('editMessage').value;
            data.event_date = document.getElementById('editEventDate').value || null;
            data.event_time = document.getElementById('editEventTime').value || null;
        }

        console.log('Updating timeline:', timelineId, data);

        // Show loading state
        var submitBtn = form.querySelector('button[type="submit"]');
        var originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengupdate...';

        fetch('/dash/invitations/' + invitationId + '/love-story/' + timelineId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': data._token
            },
            body: JSON.stringify(data)
        })
        .then(function(response) {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(function(result) {
            console.log('Update success:', result);
            
            // Reset button
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            
            if (result.success) {
                // Close modal
                var modalEl = document.getElementById('editTimelineModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
                
                // Update DOM
                updateTimelineItemInDOM(result.timeline);
                
                // Show success
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: result.message
                });
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            
            // Reset button
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat mengupdate timeline.'
            });
        });
    }

    // Delete Timeline - using vanilla JS fetch
    window.deleteTimeline = function(timelineId) {
        Swal.fire({
            icon: 'warning',
            title: 'Hapus Timeline?',
            text: 'Timeline item ini akan dihapus permanen.',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then(function(result) {
            if (result.isConfirmed) {
                var token = document.querySelector('meta[name="csrf-token"]');
                var csrfToken = token ? token.getAttribute('content') : document.querySelector('[name="_token"]').value;
                
                fetch('/dash/invitations/' + invitationId + '/love-story/' + timelineId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        _method: 'DELETE'
                    })
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(result) {
                    console.log('Delete success:', result);
                    if (result.success) {
                        // Remove from DOM
                        var item = document.querySelector('[data-id="' + timelineId + '"]');
                        if (item) {
                            item.style.transition = 'opacity 0.3s';
                            item.style.opacity = '0';
                            setTimeout(function() {
                                item.remove();
                                
                                // Check if list is empty
                                var timelineList = document.getElementById('timelineList');
                                if (timelineList && timelineList.children.length === 0) {
                                    location.reload();
                                }
                            }, 300);
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: result.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menghapus timeline.'
                    });
                });
            }
        });
    };

    // Edit Timeline
    window.editTimeline = function(id, sender, message, isTimeskip, timeskipLabel, eventDate, eventTime) {
        document.getElementById('editTimelineId').value = id;
        document.getElementById('editIsTimeskip').checked = isTimeskip;
        
        if (isTimeskip) {
            // Timeskip mode
            document.getElementById('editTimeskipLabelInput').value = timeskipLabel || '';
            document.getElementById('editTimeskipMessage').value = message || '';
        } else {
            // Normal mode
            document.getElementById('editSender').value = sender;
            document.getElementById('editMessage').value = message;
            document.getElementById('editEventDate').value = eventDate || '';
            document.getElementById('editEventTime').value = eventTime || '';
        }
        
        // Toggle timeskip label visibility
        toggleTimeskipFields('edit');
        
        // Show modal using Bootstrap 5 API
        var modalEl = document.getElementById('editTimelineModal');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    };

    // Add timeline item to DOM
    function addTimelineItemToDOM(timeline) {
        console.log('Adding timeline to DOM:', timeline);
        
        // Remove empty state if exists
        var emptyState = document.querySelector('.text-center.py-4');
        if (emptyState) {
            emptyState.remove();
        }
        
        // Create timeline list if doesn't exist
        var timelineList = document.getElementById('timelineList');
        if (!timelineList) {
            timelineList = document.createElement('div');
            timelineList.id = 'timelineList';
            document.getElementById('loveStoryContent').appendChild(timelineList);
        }
        
        // Check if previous item has same sender
        var lastItem = timelineList.lastElementChild;
        var showAvatar = true;
        
        if (lastItem) {
            var lastItemClasses = lastItem.className;
            var lastIsFromBride = lastItemClasses.includes('from-bride');
            var lastIsFromGroom = lastItemClasses.includes('from-groom') && !lastIsFromBride;
            
            var currentIsFromBride = timeline.is_from_bride;
            var currentIsFromGroom = timeline.is_from_groom;
            
            // Hide avatar if same sender as previous
            if ((lastIsFromBride && currentIsFromBride) || (lastIsFromGroom && currentIsFromGroom)) {
                showAvatar = false;
            }
        }
        
        var html = createTimelineItemHTML(timeline, showAvatar);
        timelineList.insertAdjacentHTML('beforeend', html);
    }

    // Update timeline item in DOM
    function updateTimelineItemInDOM(timeline) {
        var item = document.querySelector('[data-id="' + timeline.id + '"]');
        if (item) {
            // Check if we need to show avatar (check previous item)
            var previousItem = item.previousElementSibling;
            var showAvatar = true;
            
            if (previousItem && previousItem.classList.contains('timeline-item')) {
                var prevIsFromBride = previousItem.classList.contains('from-bride');
                var prevIsFromGroom = previousItem.classList.contains('from-groom') && !prevIsFromBride;
                
                var currentIsFromBride = timeline.is_from_bride;
                var currentIsFromGroom = timeline.is_from_groom;
                
                if ((prevIsFromBride && currentIsFromBride) || (prevIsFromGroom && currentIsFromGroom)) {
                    showAvatar = false;
                }
            }
            
            var html = createTimelineItemHTML(timeline, showAvatar);
            var temp = document.createElement('div');
            temp.innerHTML = html;
            item.replaceWith(temp.firstElementChild);
            
            // Update next item's avatar visibility if needed
            var nextItem = temp.firstElementChild.nextElementSibling;
            if (nextItem && nextItem.classList.contains('timeline-item')) {
                var nextIsFromBride = nextItem.classList.contains('from-bride');
                var nextIsFromGroom = nextItem.classList.contains('from-groom') && !nextIsFromBride;
                
                // If next item has same sender, hide its avatar
                if ((timeline.is_from_bride && nextIsFromBride) || (timeline.is_from_groom && nextIsFromGroom)) {
                    nextItem.classList.add('no-avatar');
                    var nextAvatar = nextItem.querySelector('.timeline-avatar');
                    if (nextAvatar) {
                        nextAvatar.outerHTML = '<div class="timeline-avatar-spacer"></div>';
                    }
                    var nextHeader = nextItem.querySelector('.timeline-header');
                    if (nextHeader) {
                        nextHeader.remove();
                    }
                } else {
                    // Different sender, show avatar
                    nextItem.classList.remove('no-avatar');
                }
            }
        }
    }

    // Create timeline item HTML
    function createTimelineItemHTML(timeline, showAvatar) {
        showAvatar = showAvatar !== false; // Default to true
        
        // If this is a timeskip, render differently
        if (timeline.is_timeskip && timeline.timeskip_label) {
            return '<div class="timeline-timeskip" data-id="' + timeline.id + '" data-type="timeskip">' +
                '<span class="timeskip-drag-handle">⋮⋮</span>' +
                '<div class="timeskip-line"></div>' +
                '<div class="timeskip-label">' + escapeHtml(timeline.timeskip_label) + '</div>' +
                '<div class="timeskip-line"></div>' +
                '<div class="timeline-actions" style="justify-content:center;margin-top:8px;">' +
                    '<button type="button" class="btn btn-sm btn-outline-primary" ' +
                        'onclick="editTimeline(' + timeline.id + ', \'' + timeline.sender + '\', `' + escapeHtml(timeline.message).replace(/`/g, '\\`') + '`, ' + (timeline.is_timeskip ? 'true' : 'false') + ', `' + (timeline.timeskip_label || '').replace(/`/g, '\\`') + '`, \'' + (timeline.event_date || '') + '\', \'' + (timeline.event_time || '') + '\')">' +
                        '<i class="fa fa-edit"></i> Edit' +
                    '</button>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTimeline(' + timeline.id + ')">' +
                        '<i class="fa fa-trash"></i>' +
                    '</button>' +
                '</div>' +
            '</div>';
        }
        
        var senderLabel = timeline.is_from_groom ? 'Mempelai Pria' : 'Mempelai Wanita';
        var senderClass = timeline.is_from_bride ? 'from-bride' : 'from-groom';
        var avatar = timeline.is_from_groom ? '♂' : '♀';
        var message = escapeHtml(timeline.message);
        var eventDate = timeline.event_date || '';
        var eventTime = timeline.event_time || '';
        var noAvatarClass = showAvatar ? '' : ' no-avatar';
        
        var avatarHtml = showAvatar 
            ? '<div class="timeline-avatar">' + avatar + '</div>'
            : '<div class="timeline-avatar-spacer"></div>';
            
        var headerHtml = showAvatar
            ? '<div class="timeline-header">' +
                '<span class="timeline-sender">' + senderLabel + '</span>' +
                '<span class="timeline-date">' + (timeline.formatted_date_time || '') + '</span>' +
              '</div>'
            : '';
        
        return '<div class="timeline-item ' + senderClass + noAvatarClass + '" data-id="' + timeline.id + '" data-type="message">' +
            '<span class="timeline-drag-handle">⋮⋮</span>' +
            avatarHtml +
            '<div class="timeline-content">' +
                headerHtml +
                '<div class="timeline-message">' + message + '</div>' +
                '<div class="timeline-actions">' +
                    '<button type="button" class="btn btn-sm btn-outline-primary" ' +
                        'onclick="editTimeline(' + timeline.id + ', \'' + timeline.sender + '\', `' + message.replace(/`/g, '\\`') + '`, ' + (timeline.is_timeskip ? 'true' : 'false') + ', `' + (timeline.timeskip_label || '').replace(/`/g, '\\`') + '`, \'' + eventDate + '\', \'' + eventTime + '\')">' +
                        '<i class="fa fa-edit"></i> Edit' +
                    '</button>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTimeline(' + timeline.id + ')">' +
                        '<i class="fa fa-trash"></i>' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>';
    }

    // Escape HTML
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Export init function
    return {
        init: init
    };

})();
