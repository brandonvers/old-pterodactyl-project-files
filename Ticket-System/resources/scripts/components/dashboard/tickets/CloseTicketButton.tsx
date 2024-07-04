import React, { useState } from 'react';
import { Actions, useStoreActions } from 'easy-peasy';
import { ApplicationStore } from '@/state';
import { httpErrorToHuman } from '@/api/http';
import Button from '@/components/elements/Button';
import ConfirmationModal from '@/components/elements/ConfirmationModal';
import { faTrash } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import CloseTicket from '@/api/tickets/CloseTicket';

interface Props {
    id: number;
    onClosed: () => any;
}

export default ({ id, onClosed }: Props) => {
    const [ visible, setVisible ] = useState(false);
    const [ isLoading, setIsLoading ] = useState(false);
    const { addFlash, addError, clearFlashes } = useStoreActions((actions: Actions<ApplicationStore>) => actions.flashes);

    const onClose = () => {
        setIsLoading(true);
        clearFlashes('tickets');
        clearFlashes('tickets:create');

        CloseTicket(id)
            .then(() => {
                setIsLoading(false);
                setVisible(false);
                onClosed();
            })
            .then(() => addFlash({
                type: 'success',
                key: 'tickets:create',
                message: 'Your ticket has been closed.',
            }))
            .catch(error => {
                addError({ key: 'tickets', message: httpErrorToHuman(error) });
                setIsLoading(false);
                setVisible(false);
            });
    };

    return (
        <>
            <ConfirmationModal
                visible={visible}
                title={'Close ticket?'}
                buttonText={'Yes, close ticket'}
                onConfirmed={onClose}
                showSpinnerOverlay={isLoading}
                onModalDismissed={() => setVisible(false)}
            >
                Are you sure you want to close this ticket?
            </ConfirmationModal>
            <Button color={'red'} size={'xsmall'} onClick={() => setVisible(true)}>
                <FontAwesomeIcon icon={faTrash} /> Close
            </Button>
        </>
    );
};
