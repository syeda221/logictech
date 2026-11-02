<?php
// Update index.blade.php
$indexPath = 'resources/views/admin_panel/repair/index.blade.php';
$indexContent = file_get_contents($indexPath);
$indexContent = str_replace("\r\n", "\n", $indexContent);

$targetBlock = "                                                    @if(\$finalDelDate)
                                                        <div class=\"mt-1\" style=\"font-size: 0.68rem; line-height: 1.25;\">
                                                            <span class=\"text-muted\" style=\"font-size: 0.65rem;\">Delivery Date:</span>
                                                            <div class=\"font-monospace fw-bold text-dark\" style=\"font-size: 0.72rem;\">{{ \$finalDelDate->format('d-M-Y') }}</div>
                                                        </div>
                                                    @endif
                                                @endif";

$replacementBlock = "                                                    @if(\$finalDelDate)
                                                        <div class=\"mt-1\" style=\"font-size: 0.68rem; line-height: 1.25;\">
                                                            <span class=\"text-muted\" style=\"font-size: 0.65rem;\">Delivery Date:</span>
                                                            <div class=\"font-monospace fw-bold text-dark\" style=\"font-size: 0.72rem;\">{{ \$finalDelDate->format('d-M-Y') }}</div>
                                                        </div>
                                                    @endif
                                                    @if(!empty(\$repair->delivery_notes))
                                                        <div class=\"mt-1 text-secondary fst-italic text-break\" style=\"font-size: 0.66rem; line-height: 1.2; max-width: 140px; margin: 0 auto;\" title=\"{{ \$repair->delivery_notes }}\">
                                                            <i class=\"fas fa-comment-alt text-muted me-1\" style=\"font-size: 0.6rem;\"></i>{{ Str::limit(\$repair->delivery_notes, 35) }}
                                                        </div>
                                                    @endif
                                                @endif";

if (strpos($indexContent, $targetBlock) !== false) {
    $indexContent = str_replace($targetBlock, $replacementBlock, $indexContent);
    echo "Index delivery_notes block replaced successfully.\n";
} else {
    echo "ERROR: Target block not found in index.blade.php!\n";
}

$targetInputNotes = '<input type="text" name="delivery_notes" class="form-control form-control-sm" placeholder="e.g. Tested in front of customer, handed over safely" style="border-radius: 8px; font-size: 0.78rem;">';
$replacementInputNotes = '<input type="text" name="delivery_notes" class="form-control form-control-sm" value="{{ $repair->delivery_notes }}" placeholder="e.g. Tested in front of customer, handed over safely" style="border-radius: 8px; font-size: 0.78rem;">';

if (strpos($indexContent, $targetInputNotes) !== false) {
    $indexContent = str_replace($targetInputNotes, $replacementInputNotes, $indexContent);
    echo "Index modal input notes prefilled.\n";
} else {
    echo "ERROR: Target input notes not found in index.blade.php!\n";
}

file_put_contents($indexPath, $indexContent);

// Update show.blade.php
$showPath = 'resources/views/admin_panel/repair/show.blade.php';
$showContent = file_get_contents($showPath);
$showContent = str_replace("\r\n", "\n", $showContent);

$targetShowDelRow = "<span class=\"doc-kv-label\">Delivered Date:</span>
                                        <span class=\"doc-kv-value text-success fw-bold font-monospace\">{{ \$repair->delivered_at ? \$repair->delivered_at->format('d-M-Y') : (\$repair->updated_at ? \$repair->updated_at->format('d-M-Y') : 'Delivered') }}</span>
                                    </div>
                                    @endif";

$replacementShowDelRow = "<span class=\"doc-kv-label\">Delivered Date:</span>
                                        <span class=\"doc-kv-value text-success fw-bold font-monospace\">{{ \$repair->delivered_at ? \$repair->delivered_at->format('d-M-Y') : (\$repair->updated_at ? \$repair->updated_at->format('d-M-Y') : 'Delivered') }}</span>
                                    </div>
                                    @if(!empty(\$repair->delivery_notes))
                                    <div class=\"doc-kv-row\">
                                        <span class=\"doc-kv-label\">Delivery Remarks:</span>
                                        <span class=\"doc-kv-value text-dark fw-semibold fst-italic\">{{ \$repair->delivery_notes }}</span>
                                    </div>
                                    @endif
                                    @endif";

if (strpos($showContent, $targetShowDelRow) !== false) {
    $showContent = str_replace($targetShowDelRow, $replacementShowDelRow, $showContent);
    file_put_contents($showPath, $showContent);
    echo "Show delivery_notes row added successfully.\n";
} else {
    echo "ERROR: Target show del row not found in show.blade.php!\n";
}
