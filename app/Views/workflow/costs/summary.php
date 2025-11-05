<div class="card">
    <div class="card-header">
        <h4><?php echo app_lang('cost_summary'); ?></h4>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Total Costs -->
            <div class="col-md-3">
                <div class="card dashboard-icon-widget mb-3">
                    <div class="card-body">
                        <div class="widget-icon bg-primary">
                            <i data-feather="dollar-sign"></i>
                        </div>
                        <div class="widget-details">
                            <h1><?php echo $summary['total_costs']; ?></h1>
                            <span class="bg-transparent-white"><?php echo app_lang('total_costs'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unpaid -->
            <div class="col-md-3">
                <div class="card dashboard-icon-widget mb-3">
                    <div class="card-body">
                        <div class="widget-icon bg-danger">
                            <i data-feather="alert-circle"></i>
                        </div>
                        <div class="widget-details">
                            <h1>USD <?php echo number_format($summary['unpaid_amount'], 2); ?></h1>
                            <span class="bg-transparent-white"><?php echo app_lang('unpaid'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paid (Pending Verification) -->
            <div class="col-md-3">
                <div class="card dashboard-icon-widget mb-3">
                    <div class="card-body">
                        <div class="widget-icon bg-warning">
                            <i data-feather="clock"></i>
                        </div>
                        <div class="widget-details">
                            <h1>USD <?php echo number_format($summary['paid_amount'], 2); ?></h1>
                            <span class="bg-transparent-white"><?php echo app_lang('pending_verification'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verified -->
            <div class="col-md-3">
                <div class="card dashboard-icon-widget mb-3">
                    <div class="card-body">
                        <div class="widget-icon bg-success">
                            <i data-feather="check-circle"></i>
                        </div>
                        <div class="widget-details">
                            <h1>USD <?php echo number_format($summary['verified_amount'], 2); ?></h1>
                            <span class="bg-transparent-white"><?php echo app_lang('verified'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cost Breakdown Chart -->
        <div class="card mt-3">
            <div class="card-header">
                <h5><?php echo app_lang('cost_breakdown_by_type'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="costBreakdownChart" height="100"></canvas>
            </div>
        </div>

        <!-- Transport Clearance Status -->
        <div class="card mt-3">
            <div class="card-header">
                <h5><?php echo app_lang('transport_clearance_status'); ?></h5>
            </div>
            <div class="card-body">
                <div class="alert <?php echo $summary['is_cleared'] ? 'alert-success' : 'alert-danger'; ?>">
                    <i data-feather="<?php echo $summary['is_cleared'] ? 'check-circle' : 'x-circle'; ?>" class="icon-24"></i>
                    <?php if ($summary['is_cleared']): ?>
                        <h4><?php echo app_lang('cleared_for_transport'); ?></h4>
                        <p><?php echo app_lang('all_costs_verified_proceed_to_phase_4'); ?></p>
                    <?php else: ?>
                        <h4><?php echo app_lang('not_cleared_for_transport'); ?></h4>
                        <p><?php echo app_lang('payment_verification_required_before_transport'); ?></p>
                        <ul>
                            <li><?php echo $summary['unpaid_count']; ?> <?php echo app_lang('unpaid_costs'); ?></li>
                            <li><?php echo $summary['paid_count']; ?> <?php echo app_lang('costs_pending_verification'); ?></li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Cost Breakdown Chart
        var ctx = document.getElementById('costBreakdownChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($summary['cost_types_labels']); ?>,
                datasets: [{
                    label: '<?php echo app_lang('amount'); ?> (USD)',
                    data: <?php echo json_encode($summary['cost_types_amounts']); ?>,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        feather.replace();
    });
</script>
