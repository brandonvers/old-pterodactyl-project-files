/* eslint-disable @typescript-eslint/no-unused-vars */
import React, { useEffect, useState } from 'react';
import PayPalCheckout, { usePayPalCheckout } from 'react-paypal-checkout-button';
import axios from "axios";
import useFlash from '@/plugins/useFlash';
import addBalance from '@/api/billing/postPaypal';


export default () => {

  const { addFlash, clearFlashes, clearAndAddHttpError } = useFlash();
  const [isError, setIsError] = useState(false);

  return (
    <PayPalCheckout
      intent='CAPTURE'
      /**************************************/
      /**************************************/
      clientId='PUT_HERE_PAYPAL_CLIENT_ID'  // Put here your client ID  
      amount={10}                           // Put here your default amount       
      currency='USD'                        // put here your default currency code examples (USD, EUR, GBP, AUD, CAD, etc ...)       
      /**************************************/
      /**************************************/
      onSuccess={(data, order) => {

        console.log({ data, order })

            axios.post("/api/client/billing/paypal",
                {data, order}
            ).then((res) => {
                if(res.status === 200) {
                    //window.location = res.data.forwardLink;
                } else {
                    setIsError(true);
                }
            })
            .then(() => addFlash({
                type: 'success',
                key: 'billing:success',
                message: 'You have Successfully Charged your account.',
            }))
            .catch((err) => {
                setIsError(true);
            })


      }}
      onError={(error) => {
        console.log({ error })
      }}
    />
  )
}

export const UsingHook = () => {
  const [show, setShow] = useState(false)

  const { isLoadingButton, paypalRef } = usePayPalCheckout({
    amount: 100,
    clientId: 'AS4GXXb5v8KwFT4XZJ8Yrg1xwdZpo0K9my79UetUssBH4UU0S8KWBTtV53cGoz29NX0NHdIAIvoijzSU',
    onSuccess: (data, order) => {
      console.log({ data, order })
    },
    onError: (error) => {
      console.log({ error })
    }
  })

  return (
    <>
      <button
        onClick={() => setShow(!show)}
        style={{ margin: '2rem', background: 'white', padding: '10px' }}
      >
        {show ? 'hide button' : 'show button'}
      </button>

      {show && (
        <>
          <div ref={paypalRef} />

          {isLoadingButton && <h3>loading...</h3>}
        </>
      )}
    </>
  )
}