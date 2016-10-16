<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Facebook Comments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lity/1.6.6/lity.min.css">
    <style>
        td {
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th width="25%">Name</th>
                    <th width="30%">Message</th>
                    <th width="25%">Date</th>
                    <th width="20%">Reference</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($comments as $i => $comment)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $comment['from']['name'] }}</td>
                    <td>{{ $comment['message'] }}</td>
                    <td>
                        <?php 
                            $date = $comment['created_time']->toDateTime();
                            $date = $date->setTimezone(new DateTimeZone('Asia/Bangkok'));
                            echo $date->format('Y-m-d H:i:s P');
                        ?>
                    </td>
                    <td style="word-break: break-all;">
                        @if (isset($comment['attachment']) && isset($comment['attachment']['media']['image']['src']))
                            {{ $comment['attachment']['media']['image']['src'] }}
                        @endif
                        <!--
                        Facebook User: http://facebook.com/{{ $comment['from']['id'] }}<br>
                        URL: http://facebook.com{{ $comment['id'] }} -->
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>