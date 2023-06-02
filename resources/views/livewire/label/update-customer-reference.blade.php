<div>
  <!-- Button trigger modal --> 
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
     Update Additional Reference
</button> 


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
 <div class="modal-dialog modal-lg" role="document">
   <div class="modal-content">
     <div class="modal-header">
       <h5 class="modal-title" id="exampleModalLabel">Update Additional Reference</h5>
       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
       </button>
     </div>
     {{-- <form wire:submit.prevent="submit"> --}}
         <div class="modal-body">
             <table class="table table-bordered">
                 <tr>
                     <th>@lang('orders.print-label.Barcode')</th> 
                     <th>Additional Reference #</th> 
                 </tr>
                     @foreach ($packagesRows as $key => $package)
                         <tr id="{{ $key }}">
                             <td>
                                 {{ $package['tracking_code'] }}
                             </td> 
                             <td>
                                 <input type="text" name="ids.{{ $loop->index }}" value="{{ $ids[$key]}}">
                                 {{-- @error('ids.{{ $loop->index }}') <span class="error">{{ $message }}</span> @enderror --}}
                                 <input type="text" name="refs.{{ $loop->index }}" value="{{ $refs[$key]}}">
                                 {{-- @error('ref.{{ $loop->index }}') <span class="error">{{ $message }}</span> @enderror --}}
                             </td>
                         </tr>
                     @endforeach
             </table>
         </div>
         <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
             <button type="button" class="btn btn-primary"   wire:click='submit'>Update</button>
         </div>
    {{-- </form> --}}
   </div>
 </div>
</div>
</div>