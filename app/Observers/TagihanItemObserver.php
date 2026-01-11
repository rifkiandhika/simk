<?php

namespace App\Observers;

use App\Models\TagihanItem;

class TagihanItemObserver
{
    /**
     * Handle sebelum save
     */
    public function saving(TagihanItem $item)
    {
        // Auto-calculate subtotal
        $item->subtotal = $item->qty * $item->harga;
    }

    /**
     * Handle setelah create/update/delete
     */
    public function saved(TagihanItem $item)
    {
        $this->updateTotalTagihan($item);
    }

    public function deleted(TagihanItem $item)
    {
        $this->updateTotalTagihan($item);
    }

    /**
     * Update total tagihan
     */
    protected function updateTotalTagihan(TagihanItem $item)
    {
        $tagihan = $item->tagihan;

        if ($tagihan && !$tagihan->locked) {
            $total = TagihanItem::where('id_tagihan', $tagihan->id_tagihan)
                ->sum('subtotal');

            $tagihan->total_tagihan = $total;
            $tagihan->saveQuietly();
        }
    }
}
