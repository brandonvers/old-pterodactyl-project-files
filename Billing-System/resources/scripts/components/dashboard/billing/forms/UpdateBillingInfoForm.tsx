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
import UpdateBillingInfo from '@/api/billing/UpdateBillingInfo';

export interface BillingResponse {
    billing: any[];
    user: any[];
    countries: any[];
}

interface CreateValues {
    first_name: string;
    last_name: string;
    address: string;
    city: string;
    country: string;
    zip: string;
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

    const submit = ({ first_name, last_name, address, city, country, zip }: CreateValues, { setSubmitting }: FormikHelpers<CreateValues>) => {
        clearFlashes('billing');
        clearFlashes('billing:update');
        setSubmitting(false);
        setSubmit(true);

        console.log(first_name, last_name, address, city, country, zip);

        UpdateBillingInfo(first_name, last_name, address, city, country, zip).then(() => {
            mutate();
            setSubmit(false);
        }).catch(error => {
            setSubmitting(false);
            setSubmit(false);
            clearAndAddHttpError({ key: 'billing:update', error });
        });

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
                            first_name: data.user[0]?.billing_first_name, 
                            last_name: data.user[0]?.billing_last_name, 
                            address: data.user[0]?.billing_address, 
                            city: data.user[0]?.billing_city, 
                            country: data.user[0]?.billing_country, 
                            zip: data.user[0]?.billing_zip }}
                        validationSchema={object().shape({
                            first_name: string().required(), 
                            last_name: string().required(), 
                            address: string().required(), 
                            city: string().required(), 
                            zip: string().required(),
                        })}
                    >
                        <Form>
                            <div css={tw`flex flex-wrap`}>
                                <div css={tw`w-full lg:w-6/12`}>
                                    <Field 
                                        name={'first_name'}
                                        placeholder={'First Name'}
                                        label={'First Name'}
                                    />
                                </div>
                                <div css={tw`mb-4 w-full lg:w-6/12 lg:pl-4`}>
                                    <Field 
                                        name={'last_name'}
                                        placeholder={'Last Name'}
                                        label={'Last Name'}
                                    />
                                </div>
                                <div css={tw`mb-4 w-full lg:w-6/12`}>
                                    <Field 
                                        name={'address'}
                                        placeholder={'Address'}
                                        label={'Address'}
                                    />
                                </div>
                                <div css={tw`mb-4 w-full lg:w-6/12 lg:pl-4`}>
                                    <Field 
                                        name={'city'}
                                        placeholder={'City'}
                                        label={'City'}
                                    />
                                </div>
                                <div css={tw`mb-4 w-full lg:w-6/12`}>
                                    <Label>Country</Label>
                                    <FormikFieldWrapper name={'country'}>
                                        <FormikField as={Select} name={'country'}>
                                            {data.countries.map((item, key) => (
                                                <option key={key} value={item.code}>{item.country}</option>
                                            ))}
                                        </FormikField>
                                    </FormikFieldWrapper>
                                </div>
                                <div css={tw`mb-4 w-full lg:w-6/12 lg:pl-4`}>
                                    <Field 
                                        name={'zip'}
                                        placeholder={'Zip Code'}
                                        label={'Zip Code'}
                                    />
                                </div>
                            </div>
                            <div css={tw`flex justify-end`}>
                                <Button type={'submit'} disabled={isSubmit}>Update</Button>
                            </div>
                        </Form>
                    </Formik>    
                </div>
            }
        </>
    );
};
