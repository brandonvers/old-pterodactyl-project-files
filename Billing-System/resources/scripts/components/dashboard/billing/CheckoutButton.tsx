import React, { useState } from 'react';
import { Actions, useStoreActions } from 'easy-peasy';
import { ApplicationStore } from '@/state';
import { httpErrorToHuman } from '@/api/http';
import Button from '@/components/elements/Button';
import ConfirmationModal from '@/components/elements/ConfirmationModal';
import { faShoppingCart } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import CheckoutRequest from '@/api/billing/CheckoutRequest';

interface Props {
    id: number;
    onDeleted: () => void;
}

export default ({ id, onDeleted }: Props) => {
    const [ visible, setVisible ] = useState(false);
    const [ isLoading, setIsLoading ] = useState(false);
    const { addError, clearFlashes } = useStoreActions((actions: Actions<ApplicationStore>) => actions.flashes);

    const onDelete = () => {
        setIsLoading(true);
        clearFlashes('checkout');

        CheckoutRequest()
            .then(() => {
                setIsLoading(false);
                setVisible(false);
                onDeleted();
            })
            .catch(error => {
                addError({ key: 'checkout', message: httpErrorToHuman(error) });
                setIsLoading(false);
                setVisible(false);
            });
    };

    return (
        <>
            <ConfirmationModal
                visible={visible}
                title={'Accept request?'}
                buttonText={'Yes, accept it'}
                onConfirmed={onDelete}
                showSpinnerOverlay={isLoading}
                onModalDismissed={() => setVisible(false)}
            >
                Are you sure you want to accept this request?
            </ConfirmationModal>
            <Button color={'green'} size={'xsmall'} onClick={() => setVisible(true)}>
                <FontAwesomeIcon icon={faShoppingCart} /> Checkout
            </Button>
        </>
    );
};
