import React, { useEffect, useState } from 'react';
import { RouteComponentProps } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import PageContentBlock from '@/components/elements/PageContentBlock';
import ContentBox from '@/components/elements/ContentBox';
import useFlash from '@/plugins/useFlash';
import tw from 'twin.macro';
import useSWR from 'swr';
import Spinner from '@/components/elements/Spinner';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import { Field as FormikField, Form, Formik, FormikHelpers } from 'formik';
import { Textarea } from '@/components/elements/Input';
import FormikFieldWrapper from '@/components/elements/FormikFieldWrapper';
import Button from '@/components/elements/Button';
import { breakpoint } from '@/theme';
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

import getBilling from '@/api/billing/getBilling';
//import UpdateBillingInfo from '@/api/billing/UpdateBillingInfo';

export interface BillingResponse {
    billing: any[];
    user: any[];
    countries: any[];
}

interface CreateValues {
    amount: string;
}

export default () => {

    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error, mutate } = useSWR<BillingResponse>([ '/billing' ], () => getBilling());

    const [ isSubmit, setSubmit ] = useState(false);

    useEffect(() => {
        if (!error) {
            clearFlashes('billing');
        } else {
            clearAndAddHttpError({ key: 'billing', error });
        }

        console.log(data);
    });

    const submit = ({ amount }: CreateValues, { setSubmitting }: FormikHelpers<CreateValues>) => {
        clearFlashes('billing');
        clearFlashes('billing:update');
        setSubmitting(false);
        setSubmit(true);

        console.log(amount);

        /*UpdateBillingInfo(amount).then(() => {
            mutate();
            setSubmit(false);
        }).catch(error => {
            setSubmitting(false);
            setSubmit(false);
            clearAndAddHttpError({ key: 'billing:update', error });
        });*/

    };


    return (
        <>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'billing'} css={tw`mb-4`} />
            </div>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'billing:update'} css={tw`mb-4`} />
            </div>
            {!data ?
                <div css={tw`w-full`}>
                    <Spinner size={'large'} centered />
                </div>
                :
                <div css={tw`px-1 py-2`}>
                    <Formik
                        onSubmit={submit}
                        initialValues={{ 
                            amount: ''
                        }}
                        validationSchema={object().shape({
                            amount: string().required(), 
                        })}
                    >
                        <Form>
                            <div css={tw`flex flex-wrap`}>
                                <div css={tw`mb-4 w-full`}>
                                    <Field 
                                        name={'amount'}
                                        placeholder={'Charge Amount'}
                                        label={'Charge Amount'}
                                    />
                                </div>
                            </div>
                            <div css={tw`flex justify-end`}>
                                <Button type={'submit'} disabled={isSubmit}>Add founds</Button>
                            </div>
                        </Form>
                    </Formik>    
                </div>
            }
        </>
    );
};
