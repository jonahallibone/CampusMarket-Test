<?php
use League\Fractal\TransformerAbstract;

class TokenTransformer extends TransformerAbstract
{

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform($token)
    {
        return [
            'token' => $token->token
        ];
    }

}

?>
