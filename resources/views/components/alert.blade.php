@if(session('message'))
    <div class="container-fluid">
    
        <div class="alert alert-{{ session('message')[0] }} mtop16">
            {{ session('message')[1] }}
            
            @if($errors->any())
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            @endif
            <script>
                $('.alert').slideDown();
                setTimeout(() => {
                    $('.alert').slideUp();
                },10000);
                    
                    
            </script>	
        </div>
    </div>
@endif

@push('js')
<script>
    $('.alert').slideDown();
    setTimeout(() => {
        $('.alert').slideUp();
    },10000);
        
        
</script>	
@endpush