<x-member-layout>
    <x-slot name="header">
    @php
        $now = date('Y-m-d H:i:s');
    @endphp

    </x-slot>



    <section id="reservationArea">
    <h3>その他情報</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session()->has('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

        <div class="inner">

            <section>
            <form action="{{ route('member.others.update', $member->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div>
                <p class="title">LINE</p>
                <p class="content"><input type="text" name="line" class="form-control" value="{{ $member->line }}"></p>
                </div>

                <div>
                    <p class="title">メインSNS</p>
                    <p class="content"><input type="text" name="sns" class="form-control" value="{{ $member->sns }}"></p>
                </div>

                <div>
                    <p class="title">外部リンク</p>
                    <p class="content"><input type="text" name="url" class="form-control" value="{{ $member->url }}"></p>
                </div>


            <button type="submit" class="btn btn-primary"> 更新する </button>

        </form>


            </section>



        </div>


    </section>


</section>



</x-member-layout>
