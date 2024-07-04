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

export default () => {

    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error, mutate } = useSWR<BillingResponse>([ '/billing' ], () => getBilling());

    useEffect(() => {
        if (!error) {
            clearFlashes('billing');
        } else {
            clearAndAddHttpError({ key: 'billing', error });
        }

        console.log(data);
    });


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
                <div css={tw`w-full`}>
                    <div css={tw`text-center`}>
                        <p>Account Balance</p>
                        <br></br>
                        <span dangerouslySetInnerHTML={{ __html: data.billing[0]?.code }}></span> {data.user[0]?.balance}
                   </div>
                </div>
            }
        </>
    );
};
