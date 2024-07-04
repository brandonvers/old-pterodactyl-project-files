import React, { useEffect, useState } from 'react';
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

import getTickets from '@/api/tickets/getTickets';
import CreateTicket from '@/api/tickets/CreateTicket';


const Code = styled.code`${tw`font-mono py-1 px-2 bg-neutral-900 rounded text-sm inline-block`}`;

export interface TicketsResponse {
    tickets: any[];
    categories: any[];
    priorities: any[];
}

interface CreateValues {
    title: string;
    category: string;
    priority: string;
    message: string;
}

export default () => {
    const { addFlash, clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error, mutate } = useSWR<TicketsResponse>([ '/tickets/new' ], () => getTickets());

    const [ isSubmit, setSubmit ] = useState(false);

    useEffect(() => {
        if (!error) {
            clearFlashes('tickets');
        } else {
            clearAndAddHttpError({ key: 'tickets', error });
        }
    });

    const submit = ({ title, category, priority, message }: CreateValues, { setSubmitting }: FormikHelpers<CreateValues>) => {
        clearFlashes('tickets');
        clearFlashes('tickets:create');
        setSubmitting(false);
        setSubmit(true);

        console.log(title, category, priority, message);

        CreateTicket(title, category, priority, message).then(() => {
            mutate();
            setSubmit(false);
        })
        .then(() => {
            // @ts-ignore
            window.location = '/tickets';
        })
        .then(() => addFlash({
            type: 'success',
            key: 'tickets:create',
            message: 'Your ticket has been created.',
        }))
        .catch(error => {
            setSubmitting(false);
            setSubmit(false);
            clearAndAddHttpError({ key: 'tickets:create', error });
        });

    };



    return (
        <PageContentBlock title={'New Ticket'} css={tw`flex flex-wrap`}>
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
                    <div css={tw`w-full`}>
                        <TitledGreyBox title={'New Ticket'}>
                            <div css={tw`px-1 py-2`}>
                                <Formik
                                    onSubmit={submit}
                                    initialValues={{ title: '', category: data.categories[0]?.id, priority: data.priorities[0]?.id, message: '' }}
                                    validationSchema={object().shape({
                                        title: string().required(),
                                        category: string().required(),
                                        priority: string().required(),
                                        message: string().required(),
                                    })}
                                >
                                    <Form>
                                        <div css={tw`flex flex-wrap`}>
                                            <div css={tw`mb-4 w-full lg:w-1/3`}>
                                                <Field
                                                    name={'title'}
                                                    label={'Title'}
                                                    placeholder={'Title'}
                                                />
                                            </div>
                                            <div css={tw`mb-4 w-full lg:w-1/3 lg:pl-4`}>
                                                <Label>Category</Label>
                                                <FormikFieldWrapper name={'category'}>
                                                    <FormikField as={Select} name={'category'}>
                                                        {data.categories.map((item, key) => (
                                                            <option key={key} value={item.id}>{item.name}</option>
                                                        ))}
                                                    </FormikField>
                                                </FormikFieldWrapper>
                                            </div>
                                            <div css={tw`mb-4 w-full lg:w-1/3 lg:pl-4`}>
                                                <Label>Priority</Label>
                                                <FormikFieldWrapper name={'priority'}>
                                                    <FormikField as={Select} name={'priority'}>
                                                        {data.priorities.map((item, key) => (
                                                            <option key={key} value={item.id}>{item.name}</option>
                                                        ))}
                                                    </FormikField>
                                                </FormikFieldWrapper>
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
                    </div>
                </>
            }
        </PageContentBlock>
    );
};
