import React, { useEffect, useState } from 'react';
import PageContentBlock from '@/components/elements/PageContentBlock';
import ContentBox from '@/components/elements/ContentBox';
import tw from 'twin.macro';
import FlashMessageRender from '@/components/FlashMessageRender';
import Spinner from '@/components/elements/Spinner';
import useFlash from '@/plugins/useFlash';
import useSWR from 'swr';
import { Link } from 'react-router-dom';
import Button from '@/components/elements/Button';
import TitledGreyBox from '@/components/elements/TitledGreyBox';
import MessageBox from '@/components/MessageBox';

import getStore from '@/api/billing/getStore';
import CategoriesContainer from '@/components/dashboard/billing/CategoriesContainer';
import ProductsContainer from '@/components/dashboard/billing/ProductsContainer';

export interface StoreResponse {
    billing: any[];
}

export default () => {
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data, error } = useSWR<StoreResponse>([ '/store' ], () => getStore());

    useEffect(() => {
        if (!error) {
            clearFlashes('store');
        } else {
            clearAndAddHttpError({ key: 'store', error });
        }
    });

    return (
        <PageContentBlock title={'Store'} css={tw`flex flex-wrap`}>
            <div css={tw`w-full`}>
                <FlashMessageRender byKey={'store'} css={tw`mb-4`} />
            </div>
            {!data ?
                <div css={tw`w-full`}>
                    <Spinner size={'large'} centered />
                </div>
                :
                <>
                    {data.billing[0]?.use_categories === 1 ?
                        <>
                            <CategoriesContainer/>
                        </>
                    : data.billing[0]?.use_products === 1 ?
                        <>
                            <ProductsContainer/>
                        </>
                    :
                        <div css={tw`w-full`}>
                            <MessageBox type="info" title="Info">
                                Categories and Products are disabled.
                            </MessageBox>
                        </div>
                    }
                </>
            }
        </PageContentBlock>
    );
};
