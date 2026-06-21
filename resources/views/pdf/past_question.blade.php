<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $pastQuestion->course->code }} Past Question</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 13px;
            color: #1a1a1a;
            margin: 40px 50px;
            line-height: 1.7;
            background: #fff;
        }


        /* ── WATERMARK ─────────────────────────────── */

        .watermark {
            position: fixed;
            top: 38%;
            left: 0;
            width: 100%;
            text-align: center;
            color: rgba(1, 98, 156, 0.12);
            font-size: 52px;
            font-weight: bold;
            transform: rotate(-30deg);
            letter-spacing: 2px;
        }

        .watermark-domain {
            font-size: 22px;
            margin-top: 6px;
            letter-spacing: 1px;
        }


        /* ── HEADER ────────────────────────────────── */

        .header {
            text-align: center;
            padding-bottom: 16px;
            margin-bottom: 28px;
            border-bottom: 2px solid #01629c;
        }

        .site-name {
            color: #01629c;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .site-domain {
            color: #666;
            font-size: 11px;
            margin-top: 2px;
        }

        .site-tagline {
            color: #888;
            font-size: 11px;
            margin-top: 6px;
            font-style: italic;
        }

        .school {
            margin-top: 18px;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a1a1a;
            letter-spacing: 0.5px;
        }

        .course {
            margin-top: 6px;
            font-size: 14px;
            color: #01629c;
            font-weight: bold;
        }

        .meta {
            margin-top: 8px;
            font-size: 11px;
            color: #777;
        }

        .meta span {
            margin: 0 6px;
            color: #bbb;
        }


        /* ── SECTIONS ──────────────────────────────── */

        .section {
            margin-top: 32px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #01629c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 12px;
            border-left: 4px solid #01629c;
            background: #f0f7fc;
            margin-bottom: 6px;
        }


        /* ── QUESTIONS ─────────────────────────────── */

        .question {
            margin-top: 20px;
            padding-bottom: 4px;
        }

        .question-text {
            font-size: 13px;
            color: #1a1a1a;
            line-height: 1.7;
        }

        .question-number {
            font-weight: bold;
        }

        /* child questions — one level of indent */
        .children {
            margin-top: 10px;
            margin-left: 24px;
        }

        .child-question {
            margin-top: 12px;
        }

        .child-question-text {
            font-size: 13px;
            color: #1a1a1a;
            line-height: 1.7;
        }

        .child-label {
            font-weight: bold;
        }

        /* options */
        .options {
            margin-top: 8px;
            margin-left: 24px;
        }

        .option {
            margin-bottom: 4px;
            font-size: 13px;
            color: #333;
            line-height: 1.6;
        }

        .option-label {
            font-weight: bold;
            margin-right: 4px;
        }

        /* media */
        .question-media {
            margin-top: 10px;
            margin-left: 24px;
        }

        .question-media img {
            max-width: 100%;
            height: auto;
        }

        /* marks badge */
        .marks {
            font-size: 11px;
            color: #888;
            font-style: italic;
            margin-left: 6px;
        }


        /* ── ANSWER SECTION ────────────────────────── */

        .answer-section {
            margin-top: 44px;
            padding-top: 20px;
            border-top: 2px solid #01629c;
        }

        .answer-section-title {
            font-size: 14px;
            font-weight: bold;
            color: #01629c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 18px;
        }

        .answer-group {
            margin-bottom: 20px;
        }

        .answer-group-label {
            font-size: 12px;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 4px;
        }

        .answer-row {
            font-size: 13px;
            color: #1a1a1a;
            margin-bottom: 7px;
            line-height: 1.6;
        }

        .answer-num {
            font-weight: bold;
            margin-right: 6px;
        }

        /* child answer indent */
        .child-answers {
            margin-left: 20px;
            margin-top: 4px;
        }

        .child-answer-row {
            font-size: 13px;
            color: #1a1a1a;
            margin-bottom: 5px;
            line-height: 1.6;
        }

        .child-answer-label {
            font-weight: bold;
            margin-right: 4px;
        }


        /* ── FOOTER ────────────────────────────────── */

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            padding: 6px 0 8px;
            color: #888;
            background: #fff;
        }

    </style>
</head>
<body>


{{-- WATERMARK --}}
<div class="watermark">
    {{ $siteName }}
    <div class="watermark-domain">{{ $domain }}</div>
</div>


{{-- HEADER --}}
<div class="header">
    <div class="site-name">{{ $siteName }}</div>
    <div class="site-domain">{{ $domain }}</div>
    <div class="site-tagline">Download more past questions, prepare smarter and improve your chances of success.</div>

    <div class="school">{{ $pastQuestion->school->name }}</div>

    <div class="course">
        {{ $pastQuestion->course->code }} &mdash; {{ $pastQuestion->course->title }}
    </div>

    <div class="meta">
        Session: {{ $pastQuestion->session }}
        <span>|</span>
        Semester: {{ ucfirst($pastQuestion->semester->name) }}
    </div>
</div>


{{-- QUESTIONS --}}
@foreach($pastQuestion->sections as $section)

    <div class="section">

        <div class="section-title">
            Section {{ $section->title }}
        </div>

        @foreach($section->questions as $question)

            {{-- Only render top-level (parent) questions here --}}
            @if(!$question->parent_question_id)

                <div class="question">

                    <div class="question-text">
                        <span class="question-number">{{ $loop->iteration }}.</span>
                        {!! nl2br($question->question_text) !!}
                        @if(!empty($question->marks))
                            <span class="marks">({{ $question->marks }} mark{{ $question->marks != 1 ? 's' : '' }})</span>
                        @endif
                    </div>

                    {{-- Options (MCQ) --}}
                    @if($question->options && count($question->options))
                        <div class="options">
                            @foreach($question->options as $option)
                                <div class="option">
                                    <span class="option-label">{{ chr(64 + $loop->iteration) }}.</span>
                                    {!! nl2br($option->option_text) !!}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Media --}}
                    @if($question->media && count($question->media))
                        <div class="question-media">
                            @foreach($question->media as $media)
                                <img src="{{ storage_path('app/public/' . $media->path) }}" alt="Question image">
                            @endforeach
                        </div>
                    @endif

                    {{-- Child questions (sub-parts: i, ii, iii / a, b, c) --}}
                    @if($question->children && count($question->children))
                        <div class="children">
                            @foreach($question->children as $child)

                                @php
                                    $childIndex = $loop->iteration;

                                    // Detect labelling style from child->label if present,
                                    // otherwise fall back to lowercase roman numerals
                                    if (!empty($child->label)) {
                                        $childLabel = $child->label;
                                    } else {
                                        // Build roman numeral: i, ii, iii, iv, v …
                                        $romans = ['i','ii','iii','iv','v','vi','vii','viii','ix','x',
                                                   'xi','xii','xiii','xiv','xv','xvi','xvii','xviii','xix','xx'];
                                        $childLabel = $romans[$childIndex - 1] ?? $childIndex;
                                    }
                                @endphp

                                <div class="child-question">

                                    <div class="child-question-text">
                                        <span class="child-label">({{ $childLabel }})</span>
                                        {!! nl2br($child->question_text) !!}
                                        @if(!empty($child->marks))
                                            <span class="marks">({{ $child->marks }} mark{{ $child->marks != 1 ? 's' : '' }})</span>
                                        @endif
                                    </div>

                                    {{-- Child options --}}
                                    @if($child->options && count($child->options))
                                        <div class="options">
                                            @foreach($child->options as $option)
                                                <div class="option">
                                                    <span class="option-label">{{ chr(64 + $loop->iteration) }}.</span>
                                                    {!! nl2br($option->option_text) !!}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Child media --}}
                                    @if($child->media && count($child->media))
                                        <div class="question-media">
                                            @foreach($child->media as $media)
                                                <img src="{{ storage_path('app/public/' . $media->path) }}" alt="Question image">
                                            @endforeach
                                        </div>
                                    @endif

                                </div>

                            @endforeach
                        </div>
                    @endif

                </div>

            @endif

        @endforeach

    </div>

@endforeach


{{-- ANSWERS --}}
<div class="answer-section">

    <div class="answer-section-title">Answers</div>

    @foreach($pastQuestion->sections as $section)

        <div class="answer-group">

            <div class="answer-group-label">Section {{ $section->title }}</div>

            @php $parentNum = 0; @endphp

            @foreach($section->questions as $question)

                @if(!$question->parent_question_id)

                    @php $parentNum++; @endphp

                    {{-- Parent answer (if it has a direct answer) --}}
                    @if($question->answers && $question->answers->count())
                        <div class="answer-row">
                            <span class="answer-num">{{ $parentNum }}.</span>
                            {!! nl2br($question->answers->first()->answer_text) !!}
                        </div>
                    @elseif($question->children && count($question->children))
                        {{-- Parent label only, children carry the answers --}}
                        <div class="answer-row">
                            <span class="answer-num">{{ $parentNum }}.</span>
                        </div>
                    @endif

                    {{-- Child answers --}}
                    @if($question->children && count($question->children))
                        <div class="child-answers">
                            @foreach($question->children as $child)

                                @if($child->answers && $child->answers->count())

                                    @php
                                        $ci = $loop->iteration;
                                        if (!empty($child->label)) {
                                            $cl = $child->label;
                                        } else {
                                            $romans = ['i','ii','iii','iv','v','vi','vii','viii','ix','x',
                                                       'xi','xii','xiii','xiv','xv','xvi','xvii','xviii','xix','xx'];
                                            $cl = $romans[$ci - 1] ?? $ci;
                                        }
                                    @endphp

                                    <div class="child-answer-row">
                                        <span class="child-answer-label">({{ $cl }})</span>
                                        {!! nl2br($child->answers->first()->answer_text) !!}
                                    </div>

                                @endif

                            @endforeach
                        </div>
                    @endif

                @endif

            @endforeach

        </div>

    @endforeach

</div>


{{-- FOOTER --}}
<div class="footer">
    {{ $siteName }} &nbsp;|&nbsp; {{ $domain }}
    &nbsp;&mdash;&nbsp;
    Download more past questions and academic resources at {{ $domain }}
</div>


</body>
</html>
