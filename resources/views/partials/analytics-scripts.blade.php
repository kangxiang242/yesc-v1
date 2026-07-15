<script src="{{ release_asset('static/js/track.js') }}"></script>
@hasSection('track-init')
    @yield('track-init')
@else
<script>
if (typeof Track !== 'undefined') {
    Track.init({ platform: '{{ $trackPlatform ?? 'web' }}', page_type: 'unknown' });
}
</script>
@endif
