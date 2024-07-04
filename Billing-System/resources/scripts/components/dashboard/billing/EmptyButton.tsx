import React, { useState } from 'react';
import { Actions, useStoreActions } from 'easy-peasy';
import { ApplicationStore } from '@/state';
import { httpErrorToHuman } from '@/api/http';
import Button from '@/components/elements/Button';
import ConfirmationModal from '@/components/elements/ConfirmationModal';
import { faTrash } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import EmptyCart from '@/api/billing/EmptyCart';

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
        clearFlashes('emptycart');

        EmptyCart()
            .then(() => {
                setIsLoading(false);
                setVisible(false);
                onDeleted();
            })
            .catch(error => {
                addError({ key: 'emptycart', message: httpErrorToHuman(error) });
                setIsLoading(false);
                setVisible(false);
            });
    };

    return (
        <>
            <ConfirmationModal
                visible={visible}
                title={'Empty Cart?'}
                buttonText={'Yes, Empty Cart'}
                onConfirmed={onDelete}
                showSpinnerOverlay={isLoading}
                onModalDismissed={() => setVisible(false)}
            >
                Are you sure you want to empty this cart?
            </ConfirmationModal>
            <Button color={'red'} size={'xsmall'} onClick={() => setVisible(true)}>
                <FontAwesomeIcon icon={faTrash} /> Empty Cart
            </Button>
        </>
    );
};
