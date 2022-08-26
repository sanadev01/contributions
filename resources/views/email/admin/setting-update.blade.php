@component('mail::message')
Hello, <br>
Login User details<br>
IP address: {{ $request->ip() }}<br>
Admin UserName: {{ auth()->user()->name }} {{ auth()->user()->last_name }} Pobox No.: {{ auth()->user()->pobox_number }} setting has been updated <br>
User Setting's Details<br>
Username:{{ $user->name }} {{ $user->last_name }} Pobox No.: {{ $user->pobox_number }}<br>
Which has following Diffrence <br>
<br>
@component('mail::table')
    | Setting Name  | Old Values    | New Values  |
    | :------------ |:--------------| :-----------|
 		Battery					Centered   				$10      	
 		Perfume  				Right-Aligned 			$20     
		Insurance				Centered   				$10      	  
 		USPS					Right-Aligned 			$20     
		UPS						Centered   				$10
 		Sinerlog				Right-Aligned 			$20     
		Fedex					Centered   				$10
 		tax						Right-Aligned 			$20     
		Volumetric Discount		Centered   				$10
 		UPS Profit				Right-Aligned 			$20     
 		USPS Profit				Right-Aligned 			$20     
		Discount Percentage		Centered   				$10
 		Fedex Profit			Right-Aligned 			$20     
		Weight					Centered   				$10
 		Length<					Right-Aligned 			$20     
		Width					Centered   				$10
 		Height   				Right-Aligned 			$20
@endcomponent
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Setting Name</th>
			<th>Old Values</th>
			<th>New Values</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				Battery<br>
				Perfume<br>
				Insurance<br>
				USPS<br>
				UPS<br>
				Sinerlog<br>
				Fedex<br>
				Tax<br>
				Volumetric Discount<br>
				USPS Profit<br>
				UPS Profit<br>
				Discount Percentage<br>
				Fedex Profit<br>
				Weight<br>
				Length<br>
				Width<br>
				Height
			</td>
			<td>
				<?php if($oldData['battery'] == 1) {echo "Active";}else{echo "Inactive";}?><br>
				<?php if($oldData['perfume'] == 1) {echo "Active";}else{echo "Inactive";}?><br>
				<?php if($oldData['insurance'] == 1) {echo "Active";}else{echo "Inactive";}?><br>
				<?php if($oldData['usps'] == 1) {echo "Active";}else{echo "Inactive";}?><br>
				<?php if($oldData['ups'] == 1) {echo "Active";}else{echo "Inactive";}?><br>
				<?php if($oldData['sinerlog'] == 1) {echo "Active";}else{echo "Inactive";}?><br>
				<?php if($oldData['fedex'] == 1) {echo "Active";}else{echo "Inactive";}?><br>
				<?php if($oldData['tax'] == 1) {echo "Active";}else{echo "Inactive";}?><br>
				<?php if($oldData['volumetric_discount'] == 1) {echo "Active";}else{echo "Inactive";}?><br>
				{{$oldData['usps_profit']}}<br>
				{{$oldData['ups_profit']}}<br>
				{{$oldData['discount_percentage']}}<br>
				{{$oldData['fedex_profit']}}<br>
				{{$oldData['weight']}}<br>
				{{$oldData['length']}}<br>
				{{$oldData['width']}}<br>
				{{$oldData['height']}}
			</td>
			<td>
				{{$request->battery}}<br>
				{{$request->perfume}}<br>
				{{$request->insurance}}<br>
				{{$request->usps}}<br>
				{{$request->ups}}<br>
				{{$request->sinerlog}}<br>
				{{$request->fedex}}<br>
				{{$request->tax}}<br>
				{{$request->volumetric_discount}}<br>
				{{$request->usps_profit}}<br>
				{{$request->ups_profit}}<br>
				{{$request->discount_percentage}}<br>
				{{$request->fedex_profit}}<br>
				{{$request->weight}}<br>
				{{$request->length}}<br>
				{{$request->width}}<br>
				{{$request->height}}
			</td>
		</tr>
	</tbody>
</table>
<!-- <strong>Setting Name</strong>   ||    <strong>Old Values</strong>   ||    <strong>New Values</strong> <br>
	{{ $user->email }}   ||   {{ $user->email }}    ||    {{ $user->email }}<br> -->

<br>
<strong>Date:</strong> {{ $user->updated_at->format('Y-m-d') }} <br>
<strong>Time:</strong> {{ $user->updated_at->format('g:i:s a') }}
@endcomponent
