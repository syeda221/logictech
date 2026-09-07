<!-- Modal: Delivery Product Specifications -->
<div class="modal fade" id="deliverySpecsModal" tabindex="-1" aria-labelledby="deliverySpecsModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="z-index: 1066;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header py-2.5 px-3.5 border-bottom bg-light d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="badge bg-success text-white p-2 rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                        <i class="fas fa-truck fs-6"></i>
                    </span>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2" id="deliverySpecsModalLabel">
                            <span>Delivery Specifications &amp; Dispatch</span>
                            <span class="badge bg-white text-primary border border-primary px-2 py-0.5 rounded-pill font-monospace" id="modalDeliveryInvoiceNo" style="font-size: 0.75rem;">INV-0000</span>
                        </h6>
                        <div class="text-muted small" style="font-size: 0.72rem;">Customer: <strong id="modalDeliveryCustomer" class="text-dark">-</strong></div>
                    </div>
                </div>
                <button type="button" class="close btn-close border-0 bg-transparent" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; line-height: 1; cursor: pointer; color: #64748b;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formDeliverySpecs">
                @csrf
                <input type="hidden" name="sale_id" id="modalDeliverySaleId">
                <div class="modal-body p-3.5" style="max-height: calc(85vh - 120px); overflow-y: auto;">
                    <div id="modalDeliveryLoader" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status" style="width: 2.2rem; height: 2.2rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="small text-muted mt-2 fw-semibold">Loading product items and technical specifications...</div>
                    </div>

                    <div id="modalDeliveryContent" class="d-none">
                        <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2 border-0 shadow-none" style="background-color: #f0f9ff; color: #0369a1; font-size: 0.78rem; border-radius: 8px;">
                            <i class="fas fa-info-circle text-primary fs-5"></i>
                            <span>Barah-e-karam delivery se pehle item ki specifications (Model, Serial No, Specs/Notes) darj karein. Ye details invoice par print hongi.</span>
                        </div>

                        <div class="row g-2 mb-3 p-2.5 rounded-3 border" style="background-color: #fafafa;">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary mb-1" style="font-size: 0.75rem;">
                                    <i class="far fa-calendar-alt text-primary me-1"></i> Delivery / Dispatch Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="delivery_date" id="modalDeliveryDate" class="form-control form-control-sm bg-white fw-bold text-dark" required>
                            </div>
                        </div>

                        <div class="fw-bold text-dark mb-2.5 d-flex align-items-center justify-content-between" style="font-size: 0.82rem;">
                            <span><i class="fas fa-microchip text-primary me-1.5"></i> Order Items &amp; Technical Specifications (<span id="modalDeliveryItemsCount">0</span>)</span>
                        </div>

                        <div id="modalDeliveryItemsContainer" class="d-flex flex-column gap-3">
                            {{-- Dynamically populated rows --}}
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-2.5 px-3.5 bg-light border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm px-3 fw-bold" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold shadow-sm" id="btnSubmitDeliverySpecs">
                        <i class="fas fa-check-circle me-1"></i> Save Specifications &amp; Mark Delivered
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
