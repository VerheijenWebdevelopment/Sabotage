<!doctype html>
<html>
    <head>
        <style>
            #card-generation-test {
                display: flex;
                padding: 30px;
                flex-wrap: wrap;
                flex-direction: row;
                box-sizing: border-box;
                margin: 0 -15px -30px -15px;
            }
            #card-generation-test .card-wrapper {
                box-sizing: border-box;
                padding: 0 15px 30px 15px;
            }
            #card-generation-test .card-wrapper img {
                width: 130px;
                height: 200px;
                border-radius: 5px;
            }
            #card-generation-test .card-wrapper .card-id {
                text-align: center;
                margin: 15px 0 0 0;
            }
        </style>
    </head>
    <body>
        <div id="card-generation-test">
            <div class="card-wrapper">
                <img src="{{ route('api.cards.generate-image', 0) }}">
            </div>
            @foreach ($cards as $card)
                <div class="card-wrapper">
                    <img src="{{ route('api.cards.generate-image', $card->id) }}">
                    <div class="card-id">{{ $card->id }}</div>
                </div>
            @endforeach
        </div>
    </body>
</html>