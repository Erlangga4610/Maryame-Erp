import Sortable from 'sortablejs';

window.Sortable = Sortable;

function initKanban() {
    document.querySelectorAll('[data-column]').forEach(col => {
        if (col.__sortable) col.__sortable.destroy();
        col.__sortable = Sortable.create(col, {
            group: 'kanban',
            animation: 200,
            ghostClass: 'opacity-50',
            onEnd: (evt) => {
                const id = evt.item.dataset.contentId;
                const column = evt.to.dataset.column;
                if (id && column) {
                    window.dispatchEvent(new CustomEvent('kanban-move', {
                        detail: { id: parseInt(id), column }
                    }));
                }
            },
        });
    });
}

document.addEventListener('livewire:init', () => {
    initKanban();

    Livewire.hook('morphed', () => initKanban());
});
