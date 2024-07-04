import React, { useEffect, useState } from 'react';
import { RouteComponentProps } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import PageContentBlock from '@/components/elements/PageContentBlock';
import useFlash from '@/plugins/useFlash';
import tw from 'twin.macro';
import useSWR from 'swr';
import Spinner from '@/components/elements/Spinner';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import { Field as FormikField, Form, Formik, FormikHelpers } from 'formik';
import { Textarea } from '@/components/elements/Input';
import FormikFieldWrapper from '@/components/elements/FormikFieldWrapper';
import Button from '@/components/elements/Button';
import { number, object, string } from 'yup';
import Field from '@/components/elements/Field';
import Label from '@/components/elements/Label';
import Select from '@/components/elements/Select';
import GreyRowBox from '@/components/elements/GreyRowBox';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faTicketAlt } from '@fortawesome/free-solid-svg-icons';
import styled from 'styled-components/macro';
import { Link } from 'react-router-dom';
import FlashMessageRender from '@/components/FlashMessageRender';
import MessageBox from '@/components/MessageBox';

import viewTicket from '@/api/tickets/viewTicket';
import CreateReply from '@/api/tickets/CreateReply';
import CloseTicketButton from '@/components/dashboard/tickets/CloseTicketButton';

const Code = styled.code`${tw`font-mono py-1 px-2 bg-neutral-900 rounded text-sm inline-block`}`;

export interface ViewTicketResponse {
    tickets: any[];
    categories: any[];
    priorities: any[];
}

interface CreateValues {
    id: string;
    message: string;
}


type Props = {
    id: string;
}

export default ({ match }: RouteComponentProps<Props>) => {

    var id = match.params.id;

    const { addFlash, clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error, mutate } = useSWR<ViewTicketResponse>([ id, '/tickets' ], ($id) => viewTicket($id));

    const [ isSubmit, setSubmit ] = useState(false);

    useEffect(() => {
        if (!error) {
            clearFlashes('tickets');
        } else {
            clearAndAddHttpError({ key: 'tickets', error });
        }

        console.log(data);
    });

    const submit = ({ id, message }: CreateValues, { setSubmitting }: FormikHelpers<CreateValues>) => {
        clearFlashes('tickets');
        clearFlashes('tickets:create');
        setSubmitting(false);
        setSubmit(true);

        console.log(id, message);

        CreateReply(id, message).then(() => {
            mutate();
            setSubmit(false);
        })
        .then(() => addFlash({
            type: 'success',
            key: 'tickets:create',
            message: 'Your reply has been created.',
        }))
        .catch(error => {
            setSubmitting(false);
            setSubmit(false);
            clearAndAddHttpError({ key: 'tickets:create', error });
        });
    };

    return (
        <PageContentBlock title={'New Reply'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'tickets'} css={tw`mb-4`} />
            </div>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'tickets:create'} css={tw`mb-4`} />
            </div>
            {!data ?
                <div css={tw`w-full`}>
                    <Spinner size={'large'} centered />
                </div>
                :
                <>
                    {data.tickets.length < 1 ?
                        <div css={tw`w-full`}>
                            <MessageBox type="info" title="Info">
                                There are no tickets.
                            </MessageBox>
                        </div>
                        :
                        <>
                        <div css={tw`w-full lg:w-8/12 mt-4 lg:mt-0`}>
                                    <TitledGreyBox title={'New Reply'}>
                                        <div css={tw`px-1 py-2`}>
                                            <Formik
                                                onSubmit={submit}
                                                initialValues={{ id: match.params.id, message: '' }}
                                                validationSchema={object().shape({
                                                    message: string().required(),
                                                })}
                                            >
                                                <Form>
                                                    <div css={tw`flex flex-wrap`}>
                                                        <div css={tw`w-full`}>
                                                            <Field
                                                                type={'hidden'}
                                                                name={'id'}
                                                            />
                                                        </div>
                                                        <div css={tw`w-full`}>
                                                            <Label>Message</Label>
                                                            <Field 
                                                                name={'message'}
                                                                placeholder={'Message'}
                                                            />
                                                        </div>
                                                    </div>
                                                    <br></br>
                                                    <div css={tw`flex justify-end`}>
                                                        <Button type={'submit'} disabled={isSubmit}>Submit</Button>
                                                    </div>
                                                </Form>
                                            </Formik>
                                        </div>
                                    </TitledGreyBox>
                                    <br></br>
                                    <TitledGreyBox title={data.tickets[0]?.title}>
                                            {data.tickets[0]?.comments.map((item: any, key: any) => (
                                                <div>
                                                    <TitledGreyBox 
                                                        title={
                                                            <p css={tw`text-sm uppercase`}>
                                                                <span css={tw`bg-neutral-700 text-xs py-1 px-2 rounded-full mr-2 mb-1`}>{item.updated_at}</span>
                                                                {item.user[0]?.name_first} {item.user[0]?.name_last} - ({item.user[0]?.username}) 
                                                             </p>
                                                        } 
                                                        key={key}
                                                    >
                                                        {item.comment}
                                                    </TitledGreyBox>
                                                    <br></br>
                                                </div>
                                            ))}
                                    </TitledGreyBox>
                        </div>

                        <div css={tw`w-full lg:w-4/12 lg:pl-4`}>
                                <TitledGreyBox title={'Ticket Info'}>
                                    <div css={tw`text-sm`}>
                                        <p>Title: <span css={tw`text-right`}>{data.tickets[0]?.title}</span></p>
                                    </div>
                                    <div css={tw`text-sm`}>
                                        <p>Ticket ID: <span css={tw`text-right`}>#{data.tickets[0]?.ticket_id}</span></p>
                                    </div>
                                    <div css={tw`text-sm`}>
                                        <p>Category: <span css={tw`text-right`}>{data.categories[0]?.name}</span></p>
                                    </div>
                                    <div css={tw`text-sm`}>
                                        <p>Priority: <span css={tw`text-right`}>{data.priorities[0]?.name}</span></p>
                                    </div>
                                    <div css={tw`text-sm`}>
                                        {data.tickets[0]?.status === 1 ?
                                            <p>Status: <span css={tw`bg-green-500 py-1 px-2 rounded text-white text-xs`}>Open</span></p>
                                        : data.tickets[0]?.status === 2 ?
                                            <p>Status: <span css={tw`bg-cyan-500 py-1 px-2 rounded text-white text-xs`}>Answered</span></p>
                                        : data.tickets[0]?.status === 3 ?
                                            <p>Status: <span css={tw`bg-primary-500 py-1 px-2 rounded text-white text-xs`}>Customer-Reply</span></p>
                                        : data.tickets[0]?.status === 4 ?
                                            <p>Status: <span css={tw`bg-yellow-500 py-1 px-2 rounded text-white text-xs`}>On Hold</span></p>
                                        : data.tickets[0]?.status === 5 ?
                                            <p>Status: <span css={tw`bg-neutral-500 py-1 px-2 rounded text-white text-xs`}>In Progress</span></p>
                                        : 
                                            <p>Status: <span css={tw`bg-red-500 py-1 px-2 rounded text-white text-xs`}>Closed</span></p>
                                        }
                                    </div>
                                    <div css={tw`text-sm`}>
                                        <p>Created on: <span css={tw`text-right`}>{data.tickets[0]?.created_at}</span></p>
                                    </div>
                                    <div css={tw`text-sm`}>
                                        <p>Updated on: <span css={tw`text-right`}>{data.tickets[0]?.updated_at}</span></p>
                                    </div>
                                    <br></br>
                                    {data.tickets[0]?.status !== 0 ?
                                    <div css={tw`flex justify-end text-right`}>
                                        <CloseTicketButton id={data.tickets[0]?.id} onClosed={() => mutate()}></CloseTicketButton>
                                    </div>
                                    : null
                                    }                        
                                </TitledGreyBox>
                        </div>
                        </>
                    } 
                </>
            }
        </PageContentBlock>
    );
};
