// Complete Badge Fix - Copy this into browser console to test

// Test 1: Check if badge exists
var badge = document.getElementById('notifBadge');
console.log('Badge element:', badge);
console.log('Badge classes:', badge ? badge.className : 'NOT FOUND');

// Test 2: Force show badge
if (badge) {
    badge.textContent = '1';
    badge.classList.remove('d-none');
    badge.style.cssText = 'display: inline-block !important; visibility: visible !important;';
    console.log('Badge should be visible now');
    console.log('Badge display style:', window.getComputedStyle(badge).display);
}

// Test 3: Check notification count
$.get('/notifications/unread-count', function(resp) {
    console.log('Unread count from server:', resp);
});

