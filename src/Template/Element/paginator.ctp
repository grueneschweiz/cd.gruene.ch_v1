<?php
$templates = $this->Paginator->getTemplates();
foreach ($templates as $templateKey => &$template) {
    if (in_array($templateKey, [
        'nextActive',
        'nextDisabled',
        'prevActive',
        'prevDisabled',
        'first',
        'last',
        'number',
        'current'
    ])) {
        $template = preg_replace('/<a /', '<a class="page-link" ', $template);
        $template = preg_replace('/^<li class="(.*)"><a /', '<li class="page-item $1"><a ', $template);
    }
}
$this->Paginator->setTemplates($templates);
?>

<div class="pagination-wrapper pt-4">
    <?php if ($this->Paginator->hasPage(null, 2)): ?>
        <nav aria-label="Page navigation" class="paginator d-flex justify-content-center">
            <ul class="pagination">
                <?= $this->Paginator->prev('«') ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next('»') ?>
            </ul>
        </nav>
    <?php endif; ?>
    <div class="d-flex justify-content-center"><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></div>
</div>
