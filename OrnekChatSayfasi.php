<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f4f7f6;
            margin-top: 20px;
        }

        .card {
            background: #fff;
            transition: .5s;
            border: 0;
            margin-bottom: 30px;
            border-radius: .55rem;
            position: relative;
            width: 100%;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 10%);
        }

        .chat-app .people-list {
            width: 280px;
            position: absolute;
            left: 0;
            top: 0;
            padding: 20px;
            z-index: 7;
        }

        .chat-app .chat {
            margin-left: 280px;
            border-left: 1px solid #eaeaea;
        }

        .people-list .chat-list li {
            padding: 10px 15px;
            list-style: none;
            border-radius: 3px;
        }

        .people-list .chat-list li:hover {
            background: #efefef;
            cursor: pointer;
        }

        .people-list .chat-list li.active {
            background: #efefef;
        }

        .people-list .chat-list img {
            width: 45px;
            border-radius: 50%;
        }

        .people-list .about {
            float: left;
            padding-left: 8px;
        }

        .people-list .status {
            color: #999;
            font-size: 13px;
        }

        .chat .chat-header {
            padding: 15px 20px;
            border-bottom: 2px solid #f4f7f6;
        }

        .chat .chat-header img {
            float: left;
            border-radius: 40px;
            width: 40px;
        }

        .chat .chat-about {
            float: left;
            padding-left: 10px;
        }

        .chat .chat-history {
            padding: 20px;
            border-bottom: 2px solid #fff;
            height: 400px;
            overflow-y: auto;
        }

        .chat .chat-history ul {
            padding: 0;
        }

        .chat .chat-history ul li {
            list-style: none;
            margin-bottom: 30px;
        }

        .chat .chat-history .message-data {
            margin-bottom: 15px;
        }

        .chat .chat-history .message-data img {
            border-radius: 40px;
            width: 40px;
        }

        .chat .chat-history .message-data-time {
            color: #434651;
            padding-left: 6px;
        }

        .chat .chat-history .message {
            color: #444;
            padding: 18px 20px;
            line-height: 26px;
            font-size: 16px;
            border-radius: 7px;
            display: inline-block;
            position: relative;
        }

        .chat .chat-history .my-message {
            background: #efefef;
        }

        .chat .chat-history .my-message:after {
            content: "";
            position: absolute;
            bottom: 100%;
            left: 30px;
            border: solid transparent;
            border-width: 10px;
            border-bottom-color: #efefef;
            margin-left: -10px;
        }

        .chat .chat-history .other-message {
            background: #e8f1f3;
            text-align: right;
        }

        .chat .chat-history .other-message:after {
            content: "";
            position: absolute;
            bottom: 100%;
            left: 93%;
            border: solid transparent;
            border-width: 10px;
            border-bottom-color: #e8f1f3;
            margin-left: -10px;
        }

        .chat .chat-message {
            padding: 20px;
        }

        .online {
            color: #86c541;
        }

        .offline {
            color: #e47297;
        }

        @media only screen and (max-width: 767px) {
            .chat-app .people-list {
                position: relative;
                width: 100%;
                height: auto;
                display: block;
            }

            .chat-app .chat {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card chat-app">
                    <div class="people-list">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Search...">
                        </div>
                        <ul class="list-unstyled chat-list mt-2 mb-0">
                            <li class="clearfix">
                                <img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="avatar">
                                <div class="about">
                                    <div class="name">Vincent Porter</div>
                                    <div class="status"><i class="fa fa-circle offline"></i> left 7 mins ago</div>
                                </div>
                            </li>
                            <li class="clearfix active">
                                <img src="https://bootdey.com/img/Content/avatar/avatar2.png" alt="avatar">
                                <div class="about">
                                    <div class="name">Aiden Chavez</div>
                                    <div class="status"><i class="fa fa-circle online"></i> online</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="chat">
                        <div class="chat-header clearfix">
                            <div class="row">
                                <div class="col-lg-6">
                                    <img src="https://bootdey.com/img/Content/avatar/avatar2.png" alt="avatar">
                                    <div class="chat-about">
                                        <h6 class="m-b-0">Aiden Chavez</h6>
                                        <small>Last seen: 2 hours ago</small>
                                    </div>
                                </div>
                                <div class="col-lg-6 text-right">
                                    <a href="#" class="btn btn-outline-secondary"><i class="fa fa-camera"></i></a>
                                    <a href="#" class="btn btn-outline-primary"><i class="fa fa-image"></i></a>
                                    <a href="#" class="btn btn-outline-info"><i class="fa fa-cogs"></i></a>
                                    <a href="#" class="btn btn-outline-warning"><i class="fa fa-question"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="chat-history">
                            <ul class="m-b-0">
                                <li class="clearfix">
                                    <div class="message-data text-right">
                                        <span class="message-data-time">10:10 AM, Today</span>
                                        <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="avatar">
                                    </div>
                                    <div class="message other-message float-right">Hi Aiden, how are you? How is the project coming along?</div>
                                </li>
                                <li class="clearfix">
                                    <div class="message-data">
                                        <span class="message-data-time">10:12 AM, Today</span>
                                    </div>
                                    <div class="message my-message">Are we meeting today?</div>
                                </li>
                                <li class="clearfix">
                                    <div class="message-data">
                                        <span class="message-data-time">10:15 AM, Today</span>
                                    </div>
                                    <div class="message my-message">Project has been already finished and I have results to show you.</div>
                                </li>
                            </ul>
                        </div>
                        <div class="chat-message clearfix">
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-send"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Enter text here...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>