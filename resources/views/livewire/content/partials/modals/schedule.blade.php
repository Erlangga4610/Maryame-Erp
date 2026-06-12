<flux:modal name="schedule-modal" wire:model="showScheduleModal" class="w-sm" wire:key="schedule-modal">
    <div class="p-6 space-y-4">
        <div>
            <h3 class="text-lg font-semibold text-zinc-800 dark:text-white">Schedule</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">Atur jadwal publikasi konten.</p>
        </div>

        <flux:field>
            <flux:label>Tanggal Publish</flux:label>
            <flux:input type="date" wire:model="scheduleDate" />
            <flux:error name="scheduleDate" />
        </flux:field>

        <flux:field>
            <flux:label>Waktu Publish</flux:label>
            <flux:input type="time" wire:model="scheduleTime" />
            <flux:error name="scheduleTime" />
        </flux:field>

        <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <flux:button type="button" variant="outline" wire:click="closeScheduleModal">
                Batal
            </flux:button>
            <flux:button type="button" variant="primary" color="pink" wire:click="confirmSchedule">
                Schedule
            </flux:button>
        </div>
    </div>
</flux:modal>
