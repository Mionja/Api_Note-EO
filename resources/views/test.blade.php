<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test</title>
</head>
<body>
    <form action="{{route('mark.store')}}" method="post" enctype="multipart/form-data">
        @csrf
        @method('post')
        Import excel file: <input type="file" name="file" require>
        <button type="submit" class="btn btn-primary" id="submit">Submit</button>
    </form>
</body>
</html>