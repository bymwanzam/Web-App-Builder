<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $page->title }}</title>
    @if($page->css_content)
    <style>
        {!! $page->css_content !!}
    </style>
    @endif
</head>
<body>
    {!! $page->html_content !!}
    @if($page->js_content)
    <script>
        {!! $page->js_content !!}
    </script>
    @endif
</body>
</html>
