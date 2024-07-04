import * as React from 'react';
import ContentBox from '@/components/elements/ContentBox';
import UpdateEmailAddressForm from '@/components/dashboard/forms/UpdateEmailAddressForm';
import ConfigureTwoFactorForm from '@/components/dashboard/forms/ConfigureTwoFactorForm';
import PageContentBlock from '@/components/elements/PageContentBlock';
import tw from 'twin.macro';
import { breakpoint } from '@/theme';
import styled from 'styled-components/macro';
import { RouteComponentProps } from 'react-router';
import MessageBox from '@/components/MessageBox';

import UpdateBillingInfoForm from '@/components/dashboard/billing/forms/UpdateBillingInfoForm';
import BillingSummary from '@/components/dashboard/billing/BillingSummary';
import Paypal from '@/components/dashboard/billing/Paypal';
import Stripe from '@/components/dashboard/billing/Stripe';

const Container = styled.div`
    ${tw`flex flex-wrap`};

    & > div {
        ${tw`w-full`};

        ${breakpoint('md')`
            width: calc(50% - 1rem);
        `}

        ${breakpoint('xl')`
            ${tw`w-auto flex-1`};
        `}
    }
`;

export default () => {
    return (
        <PageContentBlock title={'Billing Overview'}>
            <Container css={tw`mb-10`}>
            	<div css={tw`mt-8 md:mt-0 md:ml-8`}>
	                <ContentBox 
	                    css={tw`w-full`}
	                    title={'Billing Summary'}
	                    showFlashes={'account:billingsummary'}
	                >
	                	<BillingSummary/>
	                </ContentBox>
	                <br></br>
	                <ContentBox 
	                    css={tw`w-full`}
	                    title={'Add founds with paypal'}
	                    showFlashes={'account:paypal'}
	                >
	                	<Paypal/>
	                </ContentBox>
	                <br></br>
	            </div>
	            <div css={tw`mt-8 md:mt-0 md:ml-8`}>
	                <ContentBox 
	                    css={tw`w-full`}
	                    title={'Update Billing'} 
	                    showFlashes={'account:billing'}
	                >
	                    <UpdateBillingInfoForm/>
	                </ContentBox>
	            </div>
            </Container>
        </PageContentBlock>
    );
};
