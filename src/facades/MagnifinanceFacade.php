<?php

namespace DigitalDevLX\Magnifinance\facades;

/**
 * DigitalDevLx/Magniginance/facades
 *
 * @method static \DigitalDevLX\Magnifinance\Magnifinance addPartner()
 */

use App\Models\Company;
use App\User;
use Illuminate\Support\Facades\Facade;
use phpDocumentor\Reflection\Types\Static_;
use phpDocumentor\Reflection\Types\String_;

/**
 * @see \DigitalDevLX\Magnifinance\Skeleton\SkeletonClass
 * @param Company $company
 * @method static string addPartner(Company $company)
 * @method static string getPartnerToken($nif)
 * @method static string getDocumentFromPartner($id, $partnerNif)
 * @method static string getDocumentFromOwner($document_id)
 * @method static string emitDocumentFromPartner($partnerNif, User $client, array $document, string $sendToEmail)
 * @method static string emitDocumentFromOwner(array $client, array $document, string $sendToEmail)
 */

class MagnifinanceFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'magnifinance';
    }
}
