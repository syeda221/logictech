 @extends('admin_panel.layout.app')
 @section('content')
     <div class="main-content">
         <div class="main-content-inner">
             <div class="container">
                 <div class="row">
                     <div class="col-lg-12">
                         <div class="d-flex justify-content-between align-items-center mb-3">
                             <h3>Units</h3>
                             @can('units.create')
                                 <button type="button" class="btn btn-primary" data-toggle="modal"
                                     data-target="#exampleModal" id="reset">Create</button>
                             @endcan
                         </div>
                         <div class="border mt-1 shadow rounded " style="background-color: white;">
                             <div class="col-lg-12 m-auto">
                                 <div class="table-responsive mt-5 mb-5 ">
                                     <table id="default-datatable" class="table ">
                                         <thead class="text-center">
                                             <tr>
                                                 <th class="text-center">Id</th>
                                                 <th class="text-center">Name</th>
                                                 <th class="text-center">Action</th>
                                             </tr>
                                         </thead>
                                         <tbody class="text-center">
                                             @foreach ($unit as $company)
                                                 <tr>
                                                     <td class="id">{{ $company->id }}</td>
                                                     <td class="name">{{ $company->name }}</td>
                                                     <td>
                                                         @include('admin_panel.partials.action_buttons', [
                                                             'editRoute' => route('store.Unit'),
                                                             'deleteRoute' => route('delete.Unit', $company->id),
                                                             'editIsLink' => false,
                                                             'permissions' => [
                                                                 'edit' => 'units.edit',
                                                                 'delete' => 'units.delete',
                                                             ],
                                                             'dataId' => $company->id,
                                                         ])
                                                     </td>
                                                 </tr>
                                             @endforeach
                                         </tbody>
                                     </table>
                                 </div>

                             </div>

                         </div>
                     </div>

                 </div>
             </div>
         </div>
     </div>
     </div>
     </div>

     <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" >
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel">Add Units</h5>
                 </div>
                 <div class="modal-body">
                     <form class="myform" action="{{ route('store.Unit') }}" method="POST">
                         @csrf
                         <input type="hidden" name="edit_id" id="id" />
                         <div class="mb-3">
                             <label for="title" class="form-label">Title</label>
                             <input type="text" name="name" class="form-control" id="name" />
                         </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     @can('units.create')
                         <input type="submit" class="btn btn-primary save-btn">
                     @endcan
                 </div>
                 </form>
             </div>
         </div>
     </div>
     <!-- DataTable CSS -->
    

     <!-- jQuery -->
     <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<!-- DataTable JS -->
     
     
     <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/mycode.js') }}"></script>
     <script>
        // Fix ARIA focus warning on modal close
        $('.modal').on('hide.bs.modal', function () {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        });

         $(document).on('submit', '.myform', function(e) {
             e.preventDefault();
             var formdata = new FormData(this);
             url = $(this).attr('action');
             method = $(this).attr('method');
             $(this).find(':submit').attr('disabled', true);
             myAjax(url, formdata, method);
         });
         $(document).on('click', '.edit-btn', function() {

             var tr = $(this).closest("tr");
             var id = tr.find(".id").text();
             var name = tr.find(".name").text();
             $('#id').val(id); // Set the ID in the hidden input field
             $('#name').val(name)
             $("#exampleModal").modal("show")


         });
     </script>
     <script>
        // Fix ARIA focus warning on modal close
        $('.modal').on('hide.bs.modal', function () {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        });

         $(document).ready(function() {
             $('#default-datatable').DataTable({
                 "pageLength": 10,
                 "lengthMenu": [5, 10, 25, 50, 100],
                 "order": [
                     [0, 'desc']
                 ],
                 "language": {
                     "search": "Search Category:",
                     "lengthMenu": "Show _MENU_ entries"
                 }
             });
         });
     </script>
 @endsection
