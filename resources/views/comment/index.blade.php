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
        .modal .close {
            position: absolute;
            top: 0;
            right: 15px;
            font-size: 50px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Comments: {{ $count }}</h1>
        <form>
            <div class="form-inline">
                <div class="form-group">
                    <label class="sr-only" for="user">Facebook User</label>
                    <input type="text" class="form-control" id="user" name="user" placeholder="Facebook User" value="{{ $user }}">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="search">Comment</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Comment" value="{{ $search }}">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="search">Date Rage</label>
                    <input type="text" class="form-control" name="daterange" value="{{ $daterange }}" style="min-width: 350px" />
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="export"> Print mode
                    </label>
                </div>
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>
        <br>
        <form action="index.php/delete" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="return_url" value="{{ $url }}">
            <button class="btn btn-xs btn-default" type="submit">Delete</button>
            <br><br>
            <table class="table table-bordered table-hover" id="comments">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="select_all">
                        </th>
                        <td>#</td>
                        <th>Name</th>
                        <th>Message</th>
                        <th width="200">Date</th>
                        <th>Attachment</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    $offset = ($comments->currentPage() - 1) * $comments->perPage() + 1;
                 ?>
                    @foreach ($comments as $index => $comment)
                    <tr data-id="{{ $comment['id'] }}">
                        <td><input type="checkbox" name="ids[]" value="{{ $comment['id'] }}"></td>
                        <td>{{ $offset + $index }}</td>
                        <td><a href="http://facebook.com/{{ $comment['from']['id'] }}" target="_blank">{{ $comment['from']['name'] }}</a></td>
                        <td>{{ $comment['message'] }}</td>
                        <td>
                        <a href="http://facebook.com/{{ $comment['id'] }}" target="_blank">
                            <?php 
                                $date = $comment['created_time']->toDateTime();
                                $date = $date->setTimezone(new DateTimeZone('Asia/Bangkok'));
                                echo $date->format('Y-m-d H:i:s P');
                            ?>
                        </a>
                        </td>
                        <td>
                            @if (isset($comment['attachment']) && isset($comment['attachment']['media']['image']['src']))
                                <a class="image_link" href="{{ $comment['attachment']['media']['image']['src'] }}">Download</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button class="btn btn-xs btn-default" type="submit">Delete</button>
        </form>
        <p class="text-center">
            {{ $comments->appends(['search' => $search, 'user' => $user, 'daterange' => $daterange])->links() }}
        </p>
    </div>


    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" id="img_modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <img src="" alt="" class="img-responsive" style="width: 100%">
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lity/1.6.6/lity.min.js"></script>
    <script type="text/javascript">
    $(function() {
        $('#select_all').click(function () {
            $('#comments tbody input:checkbox').not(this).prop('checked', this.checked);
        });
        $('.image_link').click(function (e) {
            e.preventDefault();
            var image = $(this).attr('href');
            $('#img_modal').modal('toggle');
            $('#img_modal .modal-content img').attr('src', image);
        });
        $('input[name="daterange"]').daterangepicker({
            "timePicker": true,
            "timePicker24Hour": true,
            "timePickerIncrement": 30,
            "showCustomRangeLabel": false,
            "startDate": '{{ $start_date }}',
            "endDate": '{{ $end_date }}',
            locale: {
                format: 'DD/MM/YYYY HH:mm'
            }
        }, function(start, end, label) {
            console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
        });
    });
    </script>
</body>
</html>