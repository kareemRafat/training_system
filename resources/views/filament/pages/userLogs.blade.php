<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap");

        *,
        *:before,
        *:after {
            box-sizing: border-box;
        }

        :root {
            --c-grey-100: #f4f6f8;
            --c-grey-200: #e3e3e3;
            --c-grey-300: #b2b2b2;
            --c-grey-400: #7b7b7b;
            --c-grey-500: #3d3d3d;

            --c-blue-500: #688afd;
        }

        /* Some basic CSS overrides */
        body {
            line-height: 1.5;
            min-height: 100vh;
            /* font-family: "Outfit", sans-serif; */
            /* padding-bottom: 20vh; */
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        a {
            color: inherit;
        }

        img {
            display: block;
            max-width: 100%;
        }

        /* End basic CSS override */

        .timeline {
            width: 85%;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            display: flex;
            flex-direction: column;
            padding: 32px 0 32px 32px;
            border-right: 2px solid var(--c-grey-200);
            font-size: 1.125rem;
        }

        .timeline-item {
            display: flex;
            gap: 24px;

            &+* {
                margin-top: 24px;
            }

            &+.extra-space {
                margin-top: 48px;
            }
        }

        .new-comment {
            width: 100%;

            input {
                border: 1px solid var(--c-grey-200);
                border-radius: 6px;
                height: 48px;
                padding: 0 16px;
                width: 100%;

                &::placeholder {
                    color: var(--c-grey-300);
                }

                &:focus {
                    border-color: var(--c-grey-300);
                    outline: 0; // Don't actually do this
                    box-shadow: 0 0 0 4px var(--c-grey-100);
                }
            }
        }

        .timeline-item-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: -52px;
            flex-shrink: 0;
            overflow: hidden;
            box-shadow: 0 0 0 6px #fff;

            svg {
                width: 20px;
                height: 20px;
            }

            &.faded-icon {
                background-color: var(--c-grey-100);
                color: var(--c-grey-400);
            }

            &.filled-icon {
                background-color: var(--c-blue-500);
                color: #fff;
            }
        }

        .timeline-item-description {
            display: flex;
            padding-top: 6px;
            gap: 8px;
            color: var(--c-grey-400);

            img {
                flex-shrink: 0;
            }

            a {
                color: var(--c-grey-500);
                font-weight: 500;
                text-decoration: none;

                &:hover,
                &:focus {
                    outline: 0; // Don't actually do this
                    color: var(--c-blue-500);
                }
            }
        }

        .avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            overflow: hidden;
            aspect-ratio: 1 / 1;
            flex-shrink: 0;
            width: 40px;
            height: 40px;

            &.small {
                width: 28px;
                height: 28px;
            }

            img {
                object-fit: cover;
            }
        }

        .comment {
            margin-top: 12px;
            color: var(--c-grey-500);
            border: 1px solid var(--c-grey-200);
            box-shadow: 0 4px 4px 0 var(--c-grey-100);
            border-radius: 6px;
            padding: 16px;
            font-size: 1rem;
        }

        .button {
            border: 0;
            padding: 0;
            display: inline-flex;
            vertical-align: middle;
            margin-right: 4px;
            margin-top: 12px;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            height: 32px;
            padding: 0 8px;
            background-color: var(--c-grey-100);
            flex-shrink: 0;
            cursor: pointer;
            border-radius: 99em;

            &:hover {
                background-color: var(--c-grey-200);
            }

            &.square {
                border-radius: 50%;
                color: var(--c-grey-400);
                width: 32px;
                height: 32px;
                padding: 0;

                svg {
                    width: 24px;
                    height: 24px;
                }

                &:hover {
                    background-color: var(--c-grey-200);
                    color: var(--c-grey-500);
                }
            }
        }

        .show-replies {
            color: var(--c-grey-300);
            background-color: transparent;
            border: 0;
            padding: 0;
            margin-top: 16px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 1rem;
            cursor: pointer;

            svg {
                flex-shrink: 0;
                width: 24px;
                height: 24px;
            }

            &:hover,
            &:focus {
                color: var(--c-grey-500);
            }
        }

        .avatar-list {
            display: flex;
            align-items: center;

            &>* {
                position: relative;
                box-shadow: 0 0 0 2px #fff;
                margin-right: -8px;
            }
        }
    </style>
</head>

<body>
    <ol class="timeline">
        @foreach ($activityLogs as $activity)
            <li class="timeline-item">
                <span class="timeline-item-icon | faded-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path fill="none" d="M0 0h24v24H0z" />
                        <path fill="currentColor"
                            d="M12.9 6.858l4.242 4.243L7.242 21H3v-4.243l9.9-9.9zm1.414-1.414l2.121-2.122a1 1 0 0 1 1.414 0l2.829 2.829a1 1 0 0 1 0 1.414l-2.122 2.121-4.242-4.242z" />
                    </svg>
                </span>
                <div class="timeline-item-description">
                    <i class="avatar | small">
                        @if ($activity->action == 'إنشاء')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 10.5v6m3-3H9m4.06-7.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                            </svg>
                        @elseif($activity->action == 'تعديل' ||$activity->action == 'اضافة تعليق')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        @endif

                    </i>
                    <span>
                        <a href="#">{{ $activity->user->name }}</a>
                        قام
                        @if ($activity->action == 'إنشاء')

                            <b style="color:lightseagreen">إضافة </b>
                            الطالب
                        @elseif($activity->action == 'تعديل' || $activity->action == 'اضافة تعليق')
                            <b style="color:lightseagreen">{{ $activity->action }}</b>
                            فى بيانات
                        @endif

                        <b style="color:rgb(243, 135, 116)">{{ $student->name }}</b>
                        <br>
                        <time> بتاريخ {{ $activity->created_at->format('Y-m-d') }}</time>
                        <br>
                        <time>{{ $activity->created_at->format('h:i A') }}</time>
                    </span>
                </div>
            </li>
        @endforeach

    </ol>
</body>

</html>
