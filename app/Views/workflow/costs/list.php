<div class="card">
    <div class="card-header">
        <h4><?php echo app_lang('shipment_costs'); ?> - <?php echo $shipment_number; ?></h4>
        <button class="btn btn-primary btn-sm float-end" onclick="addCost()">
            <i data-feather="plus" class="icon-16"></i> <?php echo app_lang('add_cost'); ?>
        </button>
    </div>
    <div class="card-body">
        <?php if (count($costs) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo app_lang('cost_type'); ?></th>
                            <th><?php echo app_lang('description'); ?></th>
                            <th><?php echo app_lang('amount'); ?></th>
                            <th><?php echo app_lang('payment_status'); ?></th>
                            <th><?php echo app_lang('added_by'); ?></th>
                            <th><?php echo app_lang('date_added'); ?></th>
                            <th><?php echo app_lang('actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_amount = 0;
                        foreach ($costs as $cost): 
                            $total_amount += $cost->amount;
                        ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?php echo app_lang($cost->cost_type); ?></span></td>
                            <td><?php echo $cost->cost_description; ?></td>
                            <td class="text-end">
                                <strong><?php echo $cost->currency; ?> <?php echo number_format($cost->amount, 2); ?></strong>
                            </td>
                            <td>
                                <?php
                                $status_class = array(
                                    'unpaid' => 'danger',
                                    'paid' => 'warning',
                                    'verified' => 'success'
                                );
                                ?>
                                <span class="badge bg-<?php echo $status_class[$cost->payment_status]; ?>">
                                    <?php echo app_lang($cost->payment_status); ?>
                                </span>
                            </td>
                            <td><?php echo $cost->added_by_name; ?></td>
                            <td><?php echo format_to_date($cost->created_at, false); ?></td>
                            <td>
                                <?php if ($cost->payment_status === 'unpaid'): ?>
                                    <button type="button" class="btn btn-sm btn-warning mark-paid" 
                                            data-id="<?php echo $cost->id; ?>">
                                        <i data-feather="dollar-sign" class="icon-16"></i> <?php echo app_lang('mark_paid'); ?>
                                    </button>
                                <?php elseif ($cost->payment_status === 'paid'): ?>
                                    <button type="button" class="btn btn-sm btn-success verify-payment" 
                                            data-id="<?php echo $cost->id; ?>">
                                        <i data-feather="check-circle" class="icon-16"></i> <?php echo app_lang('verify'); ?>
                                    </button>
                                <?php else: ?>
                                    <span class="text-success">
                                        <i data-feather="check-circle" class="icon-16"></i> 
                                        <?php echo app_lang('verified'); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-info">
                            <td colspan="2" class="text-end"><strong><?php echo app_lang('total'); ?></strong></td>
                            <td class="text-end"><strong>USD <?php echo number_format($total_amount, 2); ?></strong></td>
                            <td colspan="4"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Transport Clearance Status -->
            <div class="alert <?php echo $is_cleared ? 'alert-success' : 'alert-warning'; ?> mt-3">
                <i data-feather="<?php echo $is_cleared ? 'check-circle' : 'alert-circle'; ?>" class="icon-16"></i>
                <?php if ($is_cleared): ?>
                    <strong><?php echo app_lang('transport_cleared'); ?></strong> - <?php echo app_lang('all_payments_verified'); ?>
                <?php else: ?>
                    <strong><?php echo app_lang('transport_not_cleared'); ?></strong> - <?php echo app_lang('pending_payment_verification'); ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-center text-muted p20">
                <i data-feather="dollar-sign" class="icon-32"></i>
                <p><?php echo app_lang('no_costs_added'); ?></p>
                <button class="btn btn-primary" onclick="addCost()">
                    <i data-feather="plus" class="icon-16"></i> <?php echo app_lang('add_first_cost'); ?>
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    function addCost() {
        // Open cost modal
        $.ajax({
            url: '<?php echo get_uri('workflow/cost_modal_form'); ?>',
            type: 'POST',
            data: {shipment_id: <?php echo $shipment_id; ?>},
            success: function(result) {
                // Show modal with form
                appAlert.warning('<?php echo app_lang('cost_modal_loading'); ?>');
            }
        });
    }

    $(document).ready(function() {
        // Mark as paid
        $('.mark-paid').click(function() {
            var costId = $(this).data('id');
            
            var reference = prompt('<?php echo app_lang('enter_payment_reference'); ?>');
            if (reference) {
                $.ajax({
                    url: '<?php echo get_uri('workflow/update_payment_status'); ?>',
                    type: 'POST',
                    data: {cost_id: costId, status: 'paid', payment_reference: reference},
                    success: function(result) {
                        if (result.success) {
                            appAlert.success(result.message, {duration: 10000});
                            location.reload();
                        } else {
                            appAlert.error(result.message);
                        }
                    }
                });
            }
        });

        // Verify payment
        $('.verify-payment').click(function() {
            var costId = $(this).data('id');
            
            if (confirm('<?php echo app_lang('confirm_payment_verification'); ?>')) {
                var notes = prompt('<?php echo app_lang('enter_verification_notes_optional'); ?>');
                $.ajax({
                    url: '<?php echo get_uri('workflow/verify_payment'); ?>',
                    type: 'POST',
                    data: {cost_id: costId, verification_notes: notes},
                    success: function(result) {
                        if (result.success) {
                            appAlert.success(result.message, {duration: 10000});
                            location.reload();
                        } else {
                            appAlert.error(result.message);
                        }
                    }
                });
            }
        });

        feather.replace();
    });
</script>
