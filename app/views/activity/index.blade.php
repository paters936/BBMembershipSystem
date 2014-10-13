<div class="page-header">
    <h1>Activity Log</h1>
    <h3>Door access to the main space - {{ $date->format('l jS \\of F'); }}</h3>
    <ul class="pager">
    @if ($previousDate)
        <li class="previous">{{ link_to_route('activity.index', $previousDate->format('d/m/y'). ' &larr; Previous', ['date'=>$previousDate->format('Y-m-d')]) }}</li>
    @else
        <li class="previous disabled"><a href="#">Previous</a></li>
    @endif
    @if ($nextDate)
        <li class="next">{{ link_to_route('activity.index', 'Next &rarr; '.$nextDate->format('d/m/y'), ['date'=>$nextDate->format('Y-m-d')]) }}</li>
    @else
        <li class="next disabled"><a href="#">Next</a></li>
    @endif
    </ul>
</div>

<div class="member-grid">
    <div class="row">
        @foreach ($logEntries as $logEntry)
        <div class="col-sm-6 col-md-4 col-lg-2">
            <div class="thumbnail">
                @if ($logEntry->user->profile->profile_photo)
                    <img src="{{ \BB\Helpers\UserImage::thumbnailUrl($logEntry->user->hash) }}" width="120" height="120" class="img-circle" />
                @else
                    <img src="{{ \BB\Helpers\UserImage::anonymous() }}" width="120" height="120" class="img-circle" />
                @endif
                <div class="caption">
                    <strong><a href="{{ route('members.show', $logEntry->user->id) }}">{{ $logEntry->user->name }}</a></strong><br />
                    {{ $logEntry->created_at->toTimeString() }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
