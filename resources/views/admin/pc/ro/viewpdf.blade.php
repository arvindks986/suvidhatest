<style type="text/css">
	table td, table th{
		border:1px solid black;
	}
</style>
<link href="{{ asset('admintheme/main.css') }}" rel="stylesheet">
<div class="container">


	<br/>
	<a href="{{ route('pdfview',['download'=>'pdf']) }}">Download PDF</a>


	<table>
		<tr>
			<th>No</th>
			<th>Title</th>
			<th>Description</th>
		</tr>
		@foreach ($items as $key => $item)
		<tr>
			<td>{{ ++$key }}</td>
			<td>{{ $item->cand_name }}</td>
			<td>{{ $item->candidate_father_name }}</td>
		</tr>
		@endforeach
	</table>
	
</div>